<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine if the user can view any documents
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documents.view');
    }

    /**
     * Determine if the user can view the document
     */
    public function view(User $user, Document $document): bool
    {
        if (! $user->hasPermission('documents.view')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            // If document has no community, allow access
            if (! $document->community_id) {
                return true;
            }

            return $user->community_id === $document->community_id;
        }

        // General and Super Admin can view all
        return true;
    }

    /**
     * Determine if the user can create documents
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('documents.upload');
    }

    /**
     * Determine if the user can download documents
     */
    public function download(User $user, Document $document): bool
    {
        if (! $user->hasPermission('documents.download')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            if (! $document->community_id) {
                return true;
            }

            return $user->community_id === $document->community_id;
        }

        return true;
    }

    /**
     * Determine if the user can update the document
     */
    public function update(User $user, Document $document): bool
    {
        if (! $user->hasPermission('documents.manage')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            if (! $document->community_id) {
                return true;
            }

            return $user->community_id === $document->community_id;
        }

        return true;
    }

    /**
     * Determine if the user can delete the document
     */
    public function delete(User $user, Document $document): bool
    {
        if (! $user->hasPermission('documents.delete')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            if (! $document->community_id) {
                return true;
            }

            return $user->community_id === $document->community_id;
        }

        return true;
    }

    /**
     * Determine if the user can restore the document
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.manage');
    }

    /**
     * Determine if the user can permanently delete the document
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.manage');
    }
}
