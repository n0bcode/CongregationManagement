<?php

namespace App\Policies;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\SystemSetting;
use App\Models\User;

class SystemSettingPolicy
{
    /**
     * Determine if the user can manage system settings (including footer).
     */
    public function manage(User $user): bool
    {
        // SUPER_ADMIN has universal access
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        // Check if user has SETTINGS_MANAGE permission
        return $user->hasPermission(PermissionKey::SETTINGS_MANAGE->value);
    }
}
