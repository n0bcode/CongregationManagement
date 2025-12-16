<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ContextualActionsService
{
    /**
     * Get available actions for a model based on state and permissions
     */
    public function getActions(Model $model, $user = null): array
    {
        $user = $user ?? auth()->user();
        $actions = [];

        // Standard CRUD actions
        if (Gate::forUser($user)->allows('update', $model)) {
            $actions[] = [
                'id' => 'edit',
                'label' => 'Edit',
                'url' => $this->getEditUrl($model),
                'icon' => 'edit',
                'variant' => 'secondary',
            ];
        }

        if (Gate::forUser($user)->allows('delete', $model)) {
            $actions[] = [
                'id' => 'delete',
                'label' => 'Delete',
                'url' => $this->getDeleteUrl($model),
                'icon' => 'trash',
                'variant' => 'danger',
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'DELETE',
            ];
        }

        // Model-specific actions
        if ($model instanceof Member) {
            $actions = array_merge($actions, $this->getMemberActions($model, $user));
        }

        return $actions;
    }

    /**
     * Get member-specific actions
     */
    protected function getMemberActions(Member $member, $user): array
    {
        $actions = [];

        // Transfer action
        if (Gate::forUser($user)->allows('update', $member)) {
            $actions[] = [
                'id' => 'transfer',
                'label' => 'Transfer Community',
                'url' => route('members.show', $member) . '#transfer',
                'icon' => 'arrow-right',
                'variant' => 'secondary',
            ];
        }

        // Formation advancement (if applicable)
        if ($this->canAdvanceFormation($member)) {
            $actions[] = [
                'id' => 'advance-formation',
                'label' => 'Advance Formation Stage',
                'url' => route('members.show', $member) . '#formation',
                'icon' => 'arrow-up',
                'variant' => 'primary',
                'highlight' => true,
            ];
        }

        // Vow renewal (if upcoming)
        if ($this->hasUpcomingVowRenewal($member)) {
            $actions[] = [
                'id' => 'renew-vows',
                'label' => 'Record Vow Renewal',
                'url' => route('members.show', $member) . '#formation',
                'icon' => 'check-circle',
                'variant' => 'success',
                'highlight' => true,
            ];
        }

        // Photo management
        if (Gate::forUser($user)->allows('update', $member)) {
            if ($member->profile_photo_path) {
                $actions[] = [
                    'id' => 'change-photo',
                    'label' => 'Change Photo',
                    'url' => route('members.show', $member) . '#photo',
                    'icon' => 'camera',
                    'variant' => 'secondary',
                ];
            } else {
                $actions[] = [
                    'id' => 'add-photo',
                    'label' => 'Add Photo',
                    'url' => route('members.show', $member) . '#photo',
                    'icon' => 'camera',
                    'variant' => 'secondary',
                ];
            }
        }

        return $actions;
    }

    /**
     * Check if member can advance formation
     */
    protected function canAdvanceFormation(Member $member): bool
    {
        // Logic to determine if member is ready for next formation stage
        $latestEvent = $member->formationEvents()->latest('started_at')->first();

        if (!$latestEvent) {
            return false;
        }

        // Check if enough time has passed since last event
        $monthsSinceLastEvent = now()->diffInMonths($latestEvent->started_at);

        // Example: Postulancy requires 6 months minimum
        /** @var \App\Models\FormationEvent $latestEvent */
        if ($latestEvent->stage === \App\Enums\FormationStage::Postulancy && $monthsSinceLastEvent >= 6) {
            return true;
        }

        // Novitiate requires 12 months minimum
        if ($latestEvent->stage === \App\Enums\FormationStage::Novitiate && $monthsSinceLastEvent >= 12) {
            return true;
        }

        return false;
    }

    /**
     * Check if member has upcoming vow renewal
     */
    protected function hasUpcomingVowRenewal(Member $member): bool
    {
        $latestEvent = $member->formationEvents()
            ->whereIn('stage', [\App\Enums\FormationStage::FirstVows, \App\Enums\FormationStage::FinalVows])
            ->latest('started_at')
            ->first();

        /** @var \App\Models\FormationEvent|null $latestEvent */
        if (!$latestEvent || $latestEvent->stage === \App\Enums\FormationStage::FinalVows) {
            return false;
        }

        // Check if renewal is due within 3 months
        $monthsUntilRenewal = $latestEvent->started_at->addYear()->diffInMonths(now(), false);

        return $monthsUntilRenewal <= 3 && $monthsUntilRenewal >= 0;
    }

    /**
     * Get edit URL for model
     */
    protected function getEditUrl(Model $model): string
    {
        $routeName = $this->getResourceRouteName($model) . '.edit';

        return route($routeName, $model);
    }

    /**
     * Get delete URL for model
     */
    protected function getDeleteUrl(Model $model): string
    {
        $routeName = $this->getResourceRouteName($model) . '.destroy';

        return route($routeName, $model);
    }

    /**
     * Get resource route name for model
     */
    protected function getResourceRouteName(Model $model): string
    {
        $className = class_basename($model);

        return strtolower($className) . 's';
    }

    /**
     * Get icon SVG for action
     */
    public function getActionIcon(string $icon): string
    {
        $icons = [
            'edit' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
            'trash' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
            'arrow-right' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>',
            'arrow-up' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>',
            'check-circle' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'camera' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        ];

        return $icons[$icon] ?? '';
    }
}
