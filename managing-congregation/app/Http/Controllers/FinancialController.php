<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Expense;
use App\Services\FinancialService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function __construct(
        protected FinancialService $financialService,
        protected PdfService $pdfService
    ) {}

    /**
     * Display the financial dashboard.
     */
    public function dashboard(): View
    {
        $this->authorize('viewAny', Expense::class); // Or a specific dashboard permission

        return view('financials.dashboard');
    }

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Expense::class);

        $query = Expense::with(['community', 'creator'])
            ->orderBy('date', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by month (for monthly view)
        if ($request->filled('month')) {
            $date = \Carbon\Carbon::parse($request->month);
            $query->dateRange($date->startOfMonth(), $date->copy()->endOfMonth());
        }

        $expenses = $query->paginate(50);

        // Get unique categories for filter
        $categories = Expense::distinct()->pluck('category')->sort();

        // Calculate totals
        $totalAmount = $query->sum('amount');

        return view('financials.index', compact('expenses', 'categories', 'totalAmount'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(): View
    {
        $this->authorize('create', Expense::class);

        $communities = Community::orderBy('name')->get();

        return view('financials.create', compact('communities'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        $validated = $request->validate([
            'community_id' => ['required', 'exists:communities,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
        ]);

        // Convert amount to cents
        $validated['amount'] = (int) ($validated['amount'] * 100);
        $validated['created_by'] = Auth::id();

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create($validated);

        return redirect()->route('financials.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): View
    {
        $this->authorize('view', $expense);

        $expense->load(['community', 'creator', 'locker']);

        return view('financials.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense): View
    {
        $this->authorize('update', $expense);

        // Check if expense is locked
        if ($expense->is_locked) {
            abort(403, 'Cannot edit locked expense.');
        }

        $communities = Community::orderBy('name')->get();

        return view('financials.edit', compact('expense', 'communities'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $this->authorize('update', $expense);

        // Check if expense is locked
        if ($expense->is_locked) {
            return redirect()->route('financials.show', $expense)
                ->with('error', 'Cannot update locked expense.');
        }

        $validated = $request->validate([
            'community_id' => ['required', 'exists:communities,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        // Convert amount to cents
        $validated['amount'] = (int) ($validated['amount'] * 100);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('financials.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        // Check if expense is locked
        if ($expense->is_locked) {
            return redirect()->route('financials.index')
                ->with('error', 'Cannot delete locked expense.');
        }

        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('financials.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Show the period locking interface.
     */
    public function lockPeriodForm(Request $request): View
    {
        $this->authorize('lockPeriod', Expense::class);

        $communities = Community::orderBy('name')->get();

        // Get current month/year or from request
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $communityId = $request->input('community_id');

        $lockStatus = null;
        if ($communityId) {
            $lockStatus = $this->financialService->getPeriodLockStatus(
                (int) $communityId,
                (int) $year,
                (int) $month
            );
        }

        return view('financials.lock-period', compact('communities', 'year', 'month', 'communityId', 'lockStatus'));
    }

    /**
     * Lock a financial period.
     */
    public function lockPeriod(Request $request): RedirectResponse
    {
        $this->authorize('lockPeriod', Expense::class);

        $validated = $request->validate([
            'community_id' => ['required', 'exists:communities,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $result = $this->financialService->lockPeriod(
            (int) $validated['community_id'],
            (int) $validated['year'],
            (int) $validated['month']
        );

        if ($result['locked_count'] === 0) {
            return redirect()->back()
                ->with('warning', 'No unlocked expenses found for the selected period.');
        }

        return redirect()->back()
            ->with('success', sprintf(
                'Successfully locked %d expense(s) totaling $%s for %s.',
                $result['locked_count'],
                number_format($result['locked_amount_dollars'], 2),
                $result['period']['month_name']
            ));
    }

    /**
     * Show monthly report.
     */
    public function monthlyReport(Request $request): View
    {
        $this->authorize('viewReports', Expense::class);

        $validated = $request->validate([
            'community_id' => ['required', 'exists:communities,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $report = $this->financialService->generateMonthlyReport(
            (int) $validated['community_id'],
            (int) $validated['year'],
            (int) $validated['month']
        );

        $communities = Community::orderBy('name')->get();

        return view('financials.monthly-report', compact('report', 'communities'));
    }

    /**
     * Export monthly report as PDF.
     */
    public function exportMonthlyReport(Request $request)
    {
        $this->authorize('exportReports', Expense::class);

        $validated = $request->validate([
            'community_id' => ['required', 'exists:communities,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $report = $this->financialService->generateMonthlyReport(
            (int) $validated['community_id'],
            (int) $validated['year'],
            (int) $validated['month']
        );

        $community = Community::findOrFail($validated['community_id']);

        return $this->pdfService->generateFinancialReport($report, $community->name);
    }
}
