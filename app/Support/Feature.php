<?php

namespace App\Support;

use App\Exceptions\DisabledFeatureException;
use Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class Feature
{
    private static function getFeaturesByVisibility()
    {
        return Cache::remember('features', now()->addMinutes(10), function () {
            $features = DB::table('features')
                ->orderBy('name')
                ->get(['name', 'enabled', 'hidden']);

            $arr = [
                'visible' => [],
                'hidden' => [],
            ];

            foreach ($features as $feature) {
                if (!!$feature->hidden) {
                    $arr['hidden'][$feature->name] = !!$feature->enabled;
                } else {
                    $arr['visible'][$feature->name] = !!$feature->enabled;
                }
            }

            return $arr;
        });
    }

    public static function all(): array
    {
        $groupedFeatures = self::getFeaturesByVisibility();

        return array_merge(
            $groupedFeatures['hidden'] ?? [],
            $groupedFeatures['visible'] ?? []
        );
    }

    public static function getVisible(array $names = []): array
    {
        return collect(self::getFeaturesByVisibility()['visible'])
            ->when(!empty($names), fn($q) => $q->filter(fn($v, $k) => in_array($k, $names)))
            ->toArray();
    }

    public static function getHidden(array $names = []): array
    {
        return collect(self::getFeaturesByVisibility()['hidden'])
            ->when(!empty($names), fn($q) => $q->filter(fn($v, $k) => in_array($k, $names)))
            ->toArray();
    }

    public static function getEnabled(array $names = []): array
    {
        return collect(self::all())
            ->filter(fn($v, $k) => $v === true)
            ->when(!empty($names), fn($q) => $q->filter(fn($v, $k) => in_array($k, $names)))
            ->keys()
            ->toArray();
    }

    public static function getDisabled(array $names = []): array
    {
        return collect(self::all())
            ->filter(fn($v, $k) => $v !== true)
            ->when(!empty($names), fn($q) => $q->filter(fn($v, $k) => in_array($k, $names)))
            ->keys()
            ->toArray();
    }

    public static function isEnabled(string|array $name): bool
    {
        $featureNames = Arr::wrap($name);

        return count(self::getEnabled($featureNames)) === count($featureNames);
    }

    public static function isDisabled(string|array $name): bool
    {
        return !self::isEnabled($name);
    }

    public static function set(string $name, bool $enabled, array $options = []): void
    {
        self::setMany([
            [
                'name' => $name,
                'enabled' => (int)$enabled,
                ...(isset($options['hidden']) ? ['hidden' => (int)$options['hidden']] : []),
            ],
        ]);
    }

    public static function setMany(array $features): void
    {
        $data = [];

        foreach ($features as $feature) {
            $data[] = [
                'name' => $feature['name'],
                'enabled' => (int)($feature['enabled'] ?? false),
                ...(isset($feature['hidden']) ? ['hidden' => (int)$feature['hidden']] : []),
                'created_at' => $now = now(),
                'updated_at' => $now,
            ];
        }

        DB::table('features')
            ->upsert(
                values: $data,
                uniqueBy: ['name'],
                update: ['enabled', 'hidden', 'updated_at']
            );

        self::clearCache();
    }

    /**
     * @throws DisabledFeatureException|Throwable
     */
    public static function assertEnabled(string|array $name): void
    {
        if (!self::isEnabled($name)) {
            throw new DisabledFeatureException($name);
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('features');
    }
}
