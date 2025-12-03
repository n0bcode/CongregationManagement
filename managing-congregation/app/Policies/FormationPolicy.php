<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Models\User;

class FormationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN) || 
               $user->hasRole(UserRole::GENERAL) ||
               $user->hasRole(UserRole::DIRECTOR);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FormationEvent $formationEvent): bool
    {
        return $this->viewAny($user) || $user->hasRole(UserRole::MEMBER);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN) || 
               $user->hasRole(UserRole::GENERAL) ||
               $user->hasRole(UserRole::DIRECTOR);
    }

    /**
     * Determine whether the user can upload documents to a formation event.
     */
    public function uploadDocument(User $user, FormationEvent $formationEvent): bool
    {
        // Super admin bypass
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Check permission
        if (!$user->hasPermission(PermissionKey::FORMATION_MANAGE)) {
            return false;
        }

        // Community scoping for Directors
        if ($user->role === UserRole::DIRECTOR) {
            if ($formationEvent->member === null) {
                return false;
            }
            return $formationEvent->member->community_id === $user->community_id;
        }

        // General role can access all
        return true;
    }

    /**
     * Determine whether the user can download a formation document.
     */
    public function downloadDocument(User $user, FormationDocument $document): bool
    {
        // Super admin bypass
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Check permission
        if (!$user->hasPermission(PermissionKey::FORMATION_VIEW)) {
            return false;
        }

        // Community scoping for Directors
        if ($user->role === UserRole::DIRECTOR) {
            if ($document->formationEvent === null || $document->formationEvent->member === null) {
                return false;
            }
            return $document->formationEvent->member->community_id === $user->community_id;
        }

        // General role can access all
        return true;
    }

    /**
     * Determine whether the user can delete a formation document.
     */
    public function deleteDocument(User $user, FormationDocument $document): bool
    {
        // Super admin bypass
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Check permission
        if (!$user->hasPermission(PermissionKey::FORMATION_MANAGE)) {
            return false;
        }

        // Community scoping for Directors
        if ($user->role === UserRole::DIRECTOR) {
            if ($document->formationEvent === null || $document->formationEvent->member === null) {
                return false;
            }
            return $document->formationEvent->member->community_id === $user->community_id;
        }

        // General role can access all
        return true;
    }
}
