<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
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
}
