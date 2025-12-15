<?php

namespace App\Casts;

use App\Enums\MemberStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CaseInsensitiveMemberStatus implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MemberStatus
    {
        if (is_null($value)) {
            return null;
        }

        // Try exact match first
        $status = MemberStatus::tryFrom($value);
        
        if ($status) {
            return $status;
        }

        // Try lowercase match
        return MemberStatus::tryFrom(strtolower($value));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof MemberStatus) {
            return $value->value;
        }

        if (is_null($value)) {
            return null;
        }

        return strtolower((string) $value);
    }
}
