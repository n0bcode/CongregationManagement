<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    /**
     * Display a listing of communities.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Community::class);

        $query = Community::withCount('members');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $communities = $query->orderBy('name')->paginate(20);

        return view('communities.index', compact('communities'));
    }

    /**
     * Show the form for creating a new community.
     */
    public function create(): View
    {
        $this->authorize('create', Community::class);

        return view('communities.create');
    }

    /**
     * Store a newly created community in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Community::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Tên cộng đồng là bắt buộc.',
            'name.max' => 'Tên cộng đồng không được vượt quá 255 ký tự.',
            'location.max' => 'Địa điểm không được vượt quá 255 ký tự.',
        ]);

        $community = Community::create($validated);

        // Audit log
        AuditLog::create([
            'action' => 'community.created',
            'user_id' => auth()->id(),
            'auditable_type' => Community::class,
            'auditable_id' => $community->id,
            'target_type' => Community::class,
            'target_id' => $community->id,
            'changes' => $validated,
        ]);

        return redirect()
            ->route('communities.index')
            ->with('success', 'Cộng đồng đã được tạo thành công.');
    }

    /**
     * Display the specified community.
     */
    public function show(Community $community): View
    {
        $this->authorize('view', $community);

        // Load member count
        $community->loadCount('members');

        // Load 10 most recent members
        $recentMembers = $community->members()
            ->latest()
            ->limit(10)
            ->get();

        return view('communities.show', compact('community', 'recentMembers'));
    }

    /**
     * Show the form for editing the specified community.
     */
    public function edit(Community $community): View
    {
        $this->authorize('update', $community);

        return view('communities.edit', compact('community'));
    }

    /**
     * Update the specified community in storage.
     */
    public function update(Request $request, Community $community): RedirectResponse
    {
        $this->authorize('update', $community);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Tên cộng đồng là bắt buộc.',
            'name.max' => 'Tên cộng đồng không được vượt quá 255 ký tự.',
            'location.max' => 'Địa điểm không được vượt quá 255 ký tự.',
        ]);

        // Store old values for audit
        $oldValues = $community->only(['name', 'location']);

        $community->update($validated);

        // Audit log
        AuditLog::create([
            'action' => 'community.updated',
            'user_id' => auth()->id(),
            'auditable_type' => Community::class,
            'auditable_id' => $community->id,
            'target_type' => Community::class,
            'target_id' => $community->id,
            'changes' => [
                'old' => $oldValues,
                'new' => $validated,
            ],
        ]);

        return redirect()
            ->route('communities.show', $community)
            ->with('success', 'Cộng đồng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified community from storage.
     */
    public function destroy(Community $community): RedirectResponse
    {
        $this->authorize('delete', $community);

        // Check if community has members
        $memberCount = $community->members()->count();
        
        if ($memberCount > 0) {
            return back()->with('error', "Không thể xóa cộng đồng này vì còn {$memberCount} thành viên. Vui lòng chuyển hoặc xóa các thành viên trước.");
        }

        // Soft delete
        $community->delete();

        // Audit log
        AuditLog::create([
            'action' => 'community.deleted',
            'user_id' => auth()->id(),
            'auditable_type' => Community::class,
            'auditable_id' => $community->id,
            'target_type' => Community::class,
            'target_id' => $community->id,
        ]);

        return redirect()
            ->route('communities.index')
            ->with('success', 'Cộng đồng đã được xóa thành công.');
    }
}
