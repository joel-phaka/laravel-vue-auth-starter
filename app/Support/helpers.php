<?php
use App\Models\Setting;
use App\Casts\SettingValueCast;
use Illuminate\Support\Facades\Cache;
use App\Models\SettingGroup;

if (! function_exists('get_setting')) {
    function get_setting(string $key): ?Setting {
        $settings = Cache::remember('settings.all', now()->addMinutes(5), function () {
            return Setting::all()->keyBy('key');
        });

        $setting = $settings[$key] ?? null;

        return $setting;
    }
}

if (! function_exists('get_setting_value')) {
    function get_setting_value(string $key, mixed $default = null): mixed {
        $setting = get_setting($key);

        if (!$setting) {
            return $default;
        }

        return SettingValueCast::castValue($setting->value, $setting->type);
    }
}

if (! function_exists('set_setting')) {
    function set_setting(string $key, mixed $value, null|string|int|SettingGroup $group = null): ?Setting {
        if ($group instanceof SettingGroup && $group->exists()) {
            $group = $group->id;
        } else if (is_string($group) || is_null($group)) {
            $groupName = trim($group ?? '') ?: 'internal';
            $group = SettingGroup::where('name', $groupName)->first()?->id ?? SettingGroup::create(['name' => $groupName])?->id;
        } else if (is_int($group)) {
            $group = SettingGroup::find($group)?->id;
        }

        if (!$group) {
            throw new \Exception('Setting group not found or could not be created');
        }

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => Setting::getSettingType($value)->value,
                'setting_group_id' => $group,
            ]
        );

        return $setting;
    }
}



if (! function_exists('clear_settings_cache')) {
    function clear_settings_cache(): void {
        Cache::forget('settings.all');
    }
}