<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine if the user can view any documents
     */
    public function viewAny(User $user): bool
    {
        // Super Admin, General, and Directors can view documents
        return in_array($user->role, [
            UserRole::SUPER_ADMIN,
            UserRole::GENERAL,
            UserRole::DIRECTOR,
        ]);
    }

    /**
     * Determine if the user can view the document
     */
    public function view(User $user, Document $document): bool
    {
        // Super Admin can view all documents
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // General can view all documents (oversight)
        if ($user->role === UserRole::GENERAL) {
            return true;
        }

        // Directors can only view documents from their community
        if ($user->role === UserRole::DIRECTOR) {
            // If document has no community, allow access
            if (! $document->community_id) {
                return true;
            }

            // Check if user's community matches document's community
            return $user->community_id === $document->community_id;
        }

        return false;
    }

    /**
     * Determine if the user can create documents
     */
    public function create(User $user): bool
    {
        // Super Admin and Directors can create documents
        return in_array($user->role, [
            UserRole::SUPER_ADMIN,
            UserRole::DIRECTOR,
        ]);
    }

    /**
     * Determine if the user can update the document
     */
    public function update(User $user, Document $document): bool
    {
        // Super Admin can update all documents
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Directors can only update documents from their community
        if ($user->role === UserRole::DIRECTOR) {
            // If document has no community, allow update
            if (! $document->community_id) {
                return true;
            }

            // Check if user's community matches document's community
            return $user->community_id === $document->community_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the document
     */
    public function delete(User $user, Document $document): bool
    {
        // Super Admin can delete all documents
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Directors can only delete documents from their community
        if ($user->role === UserRole::DIRECTOR) {
            // If document has no community, allow deletion
            if (! $document->community_id) {
                return true;
            }

            // Check if user's community matches document's community
            return $user->community_id === $document->community_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the document
     */
    public function restore(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    /**
     * Determine if the user can permanently delete the document
     */
    public function forceDelete(User $user, Document $document): bool
    {
        // Only Super Admin can force delete
        return $user->role === UserRole::SUPER_ADMIN;
    }
}
