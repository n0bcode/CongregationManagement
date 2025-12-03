<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormationEventRequest;
use App\Models\FormationEvent;
use App\Models\Member;
use App\Services\FormationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class FormationController extends Controller
{
    public function __construct(
        protected FormationService $formationService
    ) {}

    public function store(StoreFormationEventRequest $request, Member $member): RedirectResponse
    {
        // Check role-based permission to create formation events
        Gate::authorize('create', FormationEvent::class);

        // Check community-scoped access to this specific member
        // Global scope filters queries, but we need explicit check for route model binding
        if ($request->user()->cannot('view', $member)) {
            abort(403);
        }

        $this->formationService->addEvent($member, $request->validated());

        return back()->with('success', 'Formation event added successfully.');
    }
}
