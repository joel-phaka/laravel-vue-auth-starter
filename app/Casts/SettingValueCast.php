<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Setting;

class SettingValueCast  implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (in_array($model->name, Setting::encrypted())) {
            return Crypt::decrypt($value);
        }

        return self::castValue($value, $attributes['type'] ?? 'string');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (in_array($model->name, Setting::encrypted())) {
            $value = Crypt::encrypt($value);
        }

        return self::prepareForStorage($value, $attributes['type'] ?? 'string');
    }

    public static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'array'   => is_array($value) ? $value : (is_array($arr = json_decode($value, true)) ? $arr : []),
            default   => $value,
        };
    }

    public static function prepareForStorage(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => ((bool) $value) ? 1 : 0,
            'integer' => (int) $value,
            'float'   => (float) $value,
            'array'   => json_encode($value),
            default   => (string) $value,
        };
    }

    public static function validationRule(string $type): string
    {
        return match ($type) {
            'boolean' => 'boolean',
            'integer' => 'integer',
            'float'   => 'numeric',
            'array'   => 'array',
            default   => 'string',
        };
    }
}
