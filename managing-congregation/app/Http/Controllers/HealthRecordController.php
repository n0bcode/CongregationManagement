<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class HealthRecordController extends Controller
{
    public function store(Request $request, Member $member): RedirectResponse
    {
        Gate::authorize('update', $member);

        $validated = $request->validate([
            'condition' => 'required|string|max:255',
            'medications' => 'nullable|string',
            'notes' => 'nullable|string',
            'recorded_at' => 'required|date',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        $data = [
            'member_id' => $member->id,
            'condition' => $validated['condition'],
            'medications' => $validated['medications'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_at' => $validated['recorded_at'],
            'recorded_by' => Auth::id(),
        ];

        // Handle document upload
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('health-records', 'private');
            $data['document_path'] = $path;
        }

        $member->healthRecords()->create($data);

        return redirect()->route('members.show', $member)
            ->with('status', 'Health record added successfully.');
    }

    public function destroy(HealthRecord $healthRecord): RedirectResponse
    {
        Gate::authorize('update', $healthRecord->member);

        // Delete document if exists
        if ($healthRecord->document_path) {
            Storage::disk('private')->delete($healthRecord->document_path);
        }

        $member = $healthRecord->member;
        $healthRecord->delete();

        return redirect()->route('members.show', $member)
            ->with('status', 'Health record deleted successfully.');
    }
}
