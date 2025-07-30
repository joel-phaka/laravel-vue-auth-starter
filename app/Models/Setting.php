<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Casts\SettingValueCast;
use App\Enums\SettingType;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setting extends Model
{
    protected $fillable = [
        'setting_group_id',
        'key',
        'value',
        'default_value',
        'type',
        'title',
        'description',
        'icon',
        'visible',
        'required',
        'order',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'value' => SettingValueCast::class,
        'default_value' => SettingValueCast::class,
        'visible' => 'boolean',
    ];

    protected $with = [
        'allowedValues',
        /*'group'*/
    ];

    protected $hidden = [
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Setting $setting) {
            if (!$setting->title) {
                $setting->title = $setting->key;
            }

            if (auth()->check()) {
                $setting->created_by_user_id = auth()->user()->id;
                $setting->updated_by_user_id = auth()->user()->id;
            }
        });

        static::updating(function (Setting $setting) {
            if (!$setting->title) {
                $setting->title = $setting->key;
            }

            if (auth()->check()) {
                $setting->updated_by_user_id = auth()->user()->id;
            }
        });

        static::saved(function () {
            Cache::forget('settings.all');
        });

        static::deleted(function () {
            Cache::forget('settings.all');
        });
    }

    public static function encrypted(): array
    {
        return [

        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'setting_group_id');
    }

    public function allowedValues(): BelongsToMany
    {
        return $this->belongsToMany(SettingValue::class, 'settings_setting_values', 'setting_id', 'setting_value_id')->withPivot('order');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function getSettingType(mixed $value): SettingType
    {
        return match (gettype($value)) {
            'boolean' => SettingType::BOOLEAN,
            'integer' => SettingType::INTEGER,
            'float' => SettingType::FLOAT,
            'array' => SettingType::ARRAY,
            default => SettingType::STRING,
        };
    }

    public function toArray(): array
    {
        $arr = parent::toArray();

        return [
            'id' => $arr['id'],
            'key' => $arr['key'],
            'value' => $arr['value'],
            'type' => $arr['type'],
            'title' => $arr['title'],
            'description' => $arr['description'],
            'icon' => $arr['icon'],
            'visible' => $arr['visible'],
            'order' => $arr['order'],
            'required' => $arr['required'],
            'group' => !($arr['group'] ?? null) ? null : Arr::except($arr['group'], ['created_at', 'updated_at']),
            'allowed_values' => Arr::map($arr['allowed_values'], fn($v) => [
                'id' => $v['id'],
                'value' => SettingValueCast::castValue($v['value'], $arr['type']),
                'title' => $v['title'],
                'description' => $v['description'],
                'order' => data_get($v, 'pivot.order'),
                'setting_value_id' => data_get($v, 'pivot.setting_value_id'),
            ]),
        ];
    }
}
