<?php

use App\Helpers\UiHelper;

if (! function_exists('money')) {
    /**
     * Format money for display
     */
    function money(int $cents, string $currency = 'USD'): string
    {
        return UiHelper::formatMoney($cents, $currency);
    }
}

if (! function_exists('status_variant')) {
    /**
     * Get status badge variant
     */
    function status_variant(string $status): string
    {
        return UiHelper::getStatusVariant($status);
    }
}

if (! function_exists('friendly_error')) {
    /**
     * Get friendly error message
     */
    function friendly_error(string $errorCode): string
    {
        return UiHelper::getFriendlyErrorMessage($errorCode);
    }
}

if (! function_exists('format_date')) {
    /**
     * Format date for display
     */
    function format_date($date, string $format = 'human'): string
    {
        return UiHelper::formatDate($date, $format);
    }
}

if (! function_exists('greeting')) {
    /**
     * Get time-based greeting
     */
    function greeting(): string
    {
        return UiHelper::getGreeting();
    }
}

if (! function_exists('initials')) {
    /**
     * Get initials from name
     */
    function initials(string $name): string
    {
        return UiHelper::getInitials($name);
    }
}

if (! function_exists('avatar_color')) {
    /**
     * Get avatar color class
     */
    function avatar_color(string $name): string
    {
        return UiHelper::getAvatarColor($name);
    }
}
