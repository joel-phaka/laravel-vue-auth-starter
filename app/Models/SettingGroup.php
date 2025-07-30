<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingGroup extends Model
{
    protected $fillable = [
        'name',
        'title',
        'description',
        'icon',
        'visible',
        'order',
    ];

    protected $with = [
        //'settings',
    ];

    protected static function booted(): void
    {
        static::creating(function (SettingGroup $settingGroup) {
            if (!$settingGroup->title) {
                $settingGroup->title = $settingGroup->name;
            }
        });

        static::updating(function (SettingGroup $settingGroup) {
            if (!$settingGroup->title) {
                $settingGroup->title = $settingGroup->name;
            }
        });
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
