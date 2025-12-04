<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    /**
     * Get a setting value by key with type casting
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, mixed $value, ?string $type = null): void
    {
        $setting = self::firstOrCreate(
            ['key' => $key],
            [
                'type' => $type ?? self::inferType($value),
                'group' => 'general',
            ]
        );

        $setting->update([
            'value' => self::prepareValue($value, $setting->type),
        ]);

        // Clear cache
        Cache::forget("system_setting_{$key}");
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete a setting
     */
    public static function forget(string $key): void
    {
        self::where('key', $key)->delete();
        Cache::forget("system_setting_{$key}");
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Cast value based on type
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'date' => \Carbon\Carbon::parse($value),
            default => $value,
        };
    }

    /**
     * Prepare value for storage
     */
    protected static function prepareValue(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            'date' => $value instanceof \Carbon\Carbon ? $value->toDateString() : $value,
            default => (string) $value,
        };
    }

    /**
     * Infer type from value
     */
    protected static function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'json',
            $value instanceof \Carbon\Carbon => 'date',
            default => 'string',
        };
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = self::pluck('key');

        foreach ($keys as $key) {
            Cache::forget("system_setting_{$key}");
        }
    }

    /**
     * Boot method to clear cache on update
     */
    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
        });
    }
}
