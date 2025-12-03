<?php

namespace App\Helpers;

/**
 * UI Helper Functions
 * Following UI/UX Sync Rules for consistent user experience
 */
class UiHelper
{
    /**
     * Format money for display
     * Store as integers (cents), display as formatted currency
     *
     * @param  int  $cents  Amount in cents
     * @param  string  $currency  Currency code
     * @return string Formatted amount
     */
    public static function formatMoney(int $cents, string $currency = 'USD'): string
    {
        $amount = $cents / 100;

        return match ($currency) {
            'USD' => '$'.number_format($amount, 2),
            'EUR' => '€'.number_format($amount, 2),
            'VND' => number_format($amount, 0).' ₫',
            default => $currency.' '.number_format($amount, 2),
        };
    }

    /**
     * Get status badge variant based on status
     * Following "Pastoral Status Card" pattern
     *
     * @param  string  $status  Status value
     * @return string Variant name
     */
    public static function getStatusVariant(string $status): string
    {
        return match (strtolower($status)) {
            'active', 'approved', 'completed', 'good' => 'peace',
            'pending', 'in_progress', 'review' => 'pending',
            'attention', 'overdue', 'expired', 'error' => 'attention',
            default => 'peace',
        };
    }

    /**
     * Get friendly error message
     * Following "Kindness in Code" principle
     *
     * @param  string  $errorCode  Error code
     * @return string User-friendly message
     */
    public static function getFriendlyErrorMessage(string $errorCode): string
    {
        return match ($errorCode) {
            '404' => 'We couldn\'t find what you\'re looking for. Please check the link and try again.',
            '403' => 'You don\'t have permission to access this page. Please contact your administrator if you need access.',
            '500' => 'We couldn\'t complete that action right now. Please try again in a moment.',
            'validation' => 'Please check the information you entered and try again.',
            'network' => 'We couldn\'t connect to the server. Please check your internet connection.',
            default => 'Something went wrong. Please try again or contact support if the problem continues.',
        };
    }

    /**
     * Format date for display
     * Following UX Spec date format patterns
     *
     * @param  \Carbon\Carbon  $date  Date to format
     * @param  string  $format  Format type
     * @return string Formatted date
     */
    public static function formatDate($date, string $format = 'human'): string
    {
        if (! $date) {
            return '';
        }

        return match ($format) {
            'human' => $date->format('M j, Y'), // Jan 1, 2024
            'full' => $date->format('l, F j, Y'), // Monday, January 1, 2024
            'short' => $date->format('m/d/Y'), // 01/01/2024
            'database' => $date->format('Y-m-d'), // 2024-01-01
            'time' => $date->format('g:i A'), // 3:30 PM
            'datetime' => $date->format('M j, Y g:i A'), // Jan 1, 2024 3:30 PM
            default => $date->format('M j, Y'),
        };
    }

    /**
     * Get greeting based on time of day
     * Following "Pastoral Efficiency" - personal touch
     *
     * @return string Greeting message
     */
    public static function getGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 17 => 'Good Afternoon',
            default => 'Good Evening',
        };
    }

    /**
     * Get icon for file type
     *
     * @param  string  $extension  File extension
     * @return string SVG icon HTML
     */
    public static function getFileIcon(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => '<svg class="w-6 h-6 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/></svg>',
            'doc', 'docx' => '<svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/></svg>',
            'xls', 'xlsx' => '<svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/></svg>',
            'jpg', 'jpeg', 'png', 'gif' => '<svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3h12v14H4V3zm2 2v10h8V5H6z"/></svg>',
            default => '<svg class="w-6 h-6 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z"/></svg>',
        };
    }

    /**
     * Truncate text with ellipsis
     *
     * @param  string  $text  Text to truncate
     * @param  int  $length  Maximum length
     * @return string Truncated text
     */
    public static function truncate(string $text, int $length = 50): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length).'...';
    }

    /**
     * Get initials from name
     * For avatar placeholders
     *
     * @param  string  $name  Full name
     * @return string Initials (max 2 characters)
     */
    public static function getInitials(string $name): string
    {
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Generate avatar color based on name
     * Consistent color for same name
     *
     * @param  string  $name  Name
     * @return string Tailwind color class
     */
    public static function getAvatarColor(string $name): string
    {
        $colors = [
            'bg-slate-500',
            'bg-emerald-500',
            'bg-amber-500',
            'bg-rose-500',
            'bg-blue-500',
        ];

        $index = ord(strtolower($name[0])) % count($colors);

        return $colors[$index];
    }
}
