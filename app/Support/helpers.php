<?php

use App\Enums\SignedUrlState;
use App\Models\Setting;
use App\Casts\SettingValueCast;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\SettingGroup;
use Illuminate\Support\Uri;

if (! function_exists('array_non_null_or_empty')) {
    /**
     * Removes null or empty values from an array.
     *
     * Filters out elements from the given array that are either empty or null.
     * Numeric values and non-empty strings are retained.
     *
     * @param array $arr The input array to be filtered.
     *
     * @return array The filtered array containing only non-null and non-empty values.
     */
    function get_non_null_or_empty(array $arr): array {
        return array_filter($arr, function($v) {
            if (is_numeric($v) || is_array($v)) {
                return true;
            } else if (is_string($v)) {
                return !!trim($v);
            }

            return !empty($v);
        });
    }
}

if (! function_exists('array_insert_before')) {
    /**
     * Insert a new key-value pair into the array before the specified key.
     *
     * This method will find the specified `$beforeKey` in the array and insert a new key-value pair
     * immediately before that key. If the `$beforeKey` is not found, the array is returned unchanged.
     *
     * @param array $array     The original array where the insertion will take place.
     * @param string $beforeKey The key before which the new key-value pair will be inserted.
     * @param string $key       The key of the new item to insert.
     * @param mixed  $value     The value of the new item to insert.
     *
     * @return array The modified array with the new key-value pair inserted before `$beforeKey`.
     */
    function array_insert_before(array $array, string $beforeKey, string $key, mixed $value): array {
        // Find the position of the $beforeKey
        $position = array_search($beforeKey, array_keys($array));

        if ($position !== false) {
            // Insert the new key-value pair before $beforeKey
            $array = array_merge(
                array_slice($array, 0, $position), // Items before $beforeKey
                [$key => $value], // The new item
                array_slice($array, $position) // Items after $beforeKey
            );
        }

        return $array;
    }
}

if (! function_exists('array_insert_after')) {
    /**
     * Insert a new key-value pair into the array after the specified key.
     *
     * This method will find the specified `$afterKey` in the array and insert a new key-value pair
     * immediately after that key. If the `$afterKey` is not found, the array is returned unchanged.
     *
     * @param array $array     The original array where the insertion will take place.
     * @param string $afterKey  The key after which the new key-value pair will be inserted.
     * @param string $key       The key of the new item to insert.
     * @param mixed  $value     The value of the new item to insert.
     *
     * @return array The modified array with the new key-value pair inserted after `$afterKey`.
     */
    function array_insert_after(array $array, string $afterKey, string $key, mixed $value): array {
        // Find the position of the $afterKey
        $position = array_search($afterKey, array_keys($array));

        if ($position !== false) {
            // Insert the new key-value pair after $afterKey
            $array = array_merge(
                array_slice($array, 0, $position + 1), // Items before $afterKey
                [$key => $value], // The new item
                array_slice($array, $position + 1) // Items after $afterKey
            );
        }

        return $array;
    }
}

if (! function_exists('str_split_by')) {
    /**
     * Splits a string into an array using a regular expression pattern.
     *
     * This method splits the input string into an array based on the specified pattern.
     * The pattern is used to determine where to split the string.
     *
     * @param string|null $string The string to split.
     * @param string $pattern The regular expression pattern to use for splitting.
     * @param bool $includeEmpty Whether to include empty strings in the result.
     *
     * @return array The array of strings resulting from the split operation.
     */
    function str_split_by(?string $string, string $pattern, $includeEmpty = false): array {
        if (!trim($string ?? '') || !trim($pattern)) return [];

        return preg_split($pattern, trim($string), -1, $includeEmpty ? PREG_SPLIT_NO_EMPTY : 0);
    }
}

if (! function_exists('validate_iso_date_time_string')) {
    /**
     * Validates whether a given string is a valid ISO 8601 date-time format.
     *
     * This method checks if the input string conforms to a valid ISO date-time pattern.
     * It supports optional time, minute, and second components based on the provided options.
     *
     * @param string|null $isoDateTimeString The date-time string to validate.
     * @param array       $options           An optional array of validation rules:<br>
     *                                       - 'is_time_optional' (bool): Whether the time part is optional (default: true).<br>
     *                                       - 'is_minute_optional' (bool): Whether the minute part is optional (default: true).<br>
     *                                       - 'is_second_optional' (bool): Whether the second part is optional (default: true).
     *
     * @return bool Returns true if the string is a valid ISO 8601 date-time format, false otherwise.
     */
    function validate_iso_date_time_string(?string $isoDateTimeString, array $options = []): bool {
        $isTimeOptional = !!data_get($options, 'is_time_optional', true);
        $isMinuteOptional = !!data_get($options, 'is_minute_optional', true);
        $isSecondOptional = !!data_get($options, 'is_second_optional', true);

        $isoDateTimeRegex = "/^([1-2]\d{3}-((02-((0[1-9])|([1-2][0-9])))|(((0[469])|11)-((0[1-9])|([1-2][0-9])|30))|(((0[13578])|1[02])-((0[1-9])|([1-2][0-9])|3[01]))))";

        $isoDateTimeRegex .= "([T\s+]([01][0-9]|2[0-3])(\:([0-5][0-9]))" . ($isMinuteOptional ? "?" : "") . "(\:([0-5][0-9]))" . ($isSecondOptional ? "?" : "") . ")" . ($isTimeOptional ? "?" : "");

        $isoDateTimeRegex .= "$/";

        return !!preg_match($isoDateTimeRegex, $isoDateTimeString) && strtotime($isoDateTimeString) !== false;
    }
}

if (! function_exists('normalise_start_date_time')) {
    /**
     * Normalizes a given start date-time string to the format 'Y-m-d H:i:s'.
     *
     * This method checks if the provided date-time string is valid and converts it
     * into a standard database-friendly format. If the input is invalid, an empty
     * string is returned.
     *
     * @param string|null $startDateTime The start date-time string to normalize.
     *
     * @return string The normalized date-time string in 'Y-m-d H:i:s' format or an empty string if invalid.
     */
    function normalise_start_date_time(string $startDateTime): string {
        if (strtotime($startDateTime) === false) return '';

        return date('Y-m-d H:i:s', strtotime($startDateTime));
    }
}

if (! function_exists('normalise_start_date_time')) {
    /**
     * Normalizes a given end date-time string to the format 'Y-m-d H:i:s'.
     *
     * If the input string contains only a date (YYYY-MM-DD), it appends '23:59:59'.
     * If it contains only an hour (YYYY-MM-DD HH), it appends ':59:59'.
     * If it contains only hours and minutes (YYYY-MM-DD HH:MM), it appends ':59'.
     * If the input is invalid, an empty string is returned.
     *
     * @param string|null $endDateTime The end date-time string to normalize.
     *
     * @return string The normalized date-time string in 'Y-m-d H:i:s' format or an empty string if invalid.
     */
    function normalise_end_date_time(string $endDateTime): string {
        if (strtotime($endDateTime) === false) return '';

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDateTime)) {
            $endDateTime .= ' 23:59:59';
        } else if (preg_match('/\d{4}-\d{2}-\d{2}[T\s]\d{2}$/', $endDateTime)) {
            $endDateTime .= ':59:59';
        } else if (preg_match('/\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}$/', $endDateTime)) {
            $endDateTime .= ':59';
        }

        return date('Y-m-d H:i:s', strtotime($endDateTime));
    }
}

if (! function_exists('enum_names')) {
    /**
     * Retrieves the names of all cases in an enum class.
     *
     * This method returns an array containing the names of all cases defined in the specified enum class.
     *
     * @param string $enumClass The fully qualified name of the enum class.
     *
     * @return array An array of strings containing the names of all cases in the enum.
     */
    function enum_names(string $enumClass): array {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("The given class is not a valid enum.");
        }

        return array_map(fn($case) => $case->name, $enumClass::cases());
    }
}

if (! function_exists('enum_values')) {
    /**
     * Retrieves the values of all cases in an enum class.
     *
     * This method returns an array containing the values of all cases defined in the specified enum class.
     *
     * @param string $enumClass The fully qualified name of the enum class.
     *
     * @return array An array of strings containing the values of all cases in the enum.
     */
    function enum_values(string $enumClass): array {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("The given class is not a valid enum.");
        }

        return array_map(fn($case) => $case->value, $enumClass::cases());
    }
}

if (! function_exists('base_url')) {
    /**
     * Constructs the base URL of the application, with an optional path.
     *
     * Determines the base URL based on the server's host and protocol information.
     * Appends the specified path to the base URL, if provided.
     *
     * @param string|null $path The optional path to append to the base URL.
     *
     * @return string The constructed base URL with the appended path.
     */
    function base_url(?string $path = ''): string
    {
        $host = data_get($_SERVER, 'HTTP_X_FORWARDED_HOST') ?: data_get($_SERVER,'HTTP_HOST');
        $proto = data_get($_SERVER, 'HTTP_X_FORWARDED_PROTO');

        if (!$proto) {
            $isSecure = data_get($_SERVER, 'HTTP_X_FORWARDED_SSL') == 'on' ||
                data_get($_SERVER, 'SERVER_PORT') == 443 ||
                !!data_get($_SERVER, 'HTTPS') && (
                    strtolower(data_get($_SERVER, 'HTTPS')) == 'on' || strtolower(data_get($_SERVER, 'HTTPS')) != 'off'
                );

            $proto = $isSecure ? "https" : "http";
        }

        $url = (!!$host ? ($proto . '://' . $host) : null);

        if (!!$url && !!($path = trim($path ?? '')) && !str_starts_with($path, '?')) {
            $path = preg_replace('/^\//', '/', $path);
            $path = preg_replace('/\/$/', '', $path);
            $path = (!!$path ? "/" . $path : null);

            $url .= $path;
        }

        return strval($url);
    }
}

if (! function_exists('is_local_url')) {
    /**
     * Constructs the base URL of the application, with an optional path.
     *
     * Determines the base URL based on the server's host and protocol information.
     * Appends the specified path to the base URL, if provided.
     *
     * @param string|null $path The optional path to append to the base URL.
     *
     * @return string The constructed base URL with the appended path.
     */
    function is_local_url(string $url): bool {
        $currentUrl = base_url();

        return parse_url($url, PHP_URL_SCHEME) === parse_url($currentUrl, PHP_URL_SCHEME) &&
            parse_url($url, PHP_URL_HOST) === parse_url($currentUrl, PHP_URL_HOST) &&
            parse_url($url, PHP_URL_PORT) === parse_url($currentUrl, PHP_URL_PORT);
    }
}

if (! function_exists('format_pagination')) {
    /**
     * Formats the given paginator instance into a structured array.
     *
     * This method transforms a LengthAwarePaginator instance by separating
     * pagination metadata from the actual data and including custom meta fields.
     *
     * @param LengthAwarePaginator $paginator The paginator instance to format.
     *
     * @return array An array containing:<br>
     *               - 'meta' (array): Pagination metadata, including:<br>
     *                 -- 'is_last_page' (bool): The current page number.<br>
     *                 -- 'has_more_pages' (bool): Whether there are more pages available.<br>
     *               - 'data' (array): The paginated items.
     */
    function format_pagination(LengthAwarePaginator $paginator): array
    {
        $paginatorArr = $paginator->toArray();
        $data = $paginatorArr['data'];

        data_forget($paginatorArr, 'data');

        $meta = array_merge($paginatorArr, [
            'is_last_page' => $paginatorArr['current_page'] == $paginatorArr['last_page'],
            'has_more_pages' => $paginator->hasMorePages()
        ]);

        return [
            'meta' => $meta,
            'data' => $data
        ];
    }
}

if (! function_exists('paginate_records')) {
    /**
     * Paginates the result of a query builder or eloquent builder and returns formatted data.
     *
     * @param QueryBuilder|EloquentBuilder $builder The query or eloquent builder instance to paginate.
     * @param array                        $options An optional array of pagination settings:<br>
     *                                              - 'per_page' (int): The number of items per page (default: 10).<br>
     *                                              - 'appends' (array): Additional query parameters to append to the pagination links.
     *
     * @return array The formatted pagination data returned from formatPagination().
     */
    function paginate_records(QueryBuilder|EloquentBuilder $builder, array $options = []): array
    {
        $options['per_page'] = intval($options['per_page'] ?? 0);
        $options['appends'] = (array)($options['appends'] ?? []);

        $options['per_page'] = ($options['per_page'] ?: intval(config('const.pagination.items_per_page'))) ?: 10;
        $maxItemsPerPages = intval(config('const.pagination.max_items_per_page')) ?: 20;

        if ($options['per_page'] > $maxItemsPerPages) {
            $options['per_page'] = $maxItemsPerPages;
        }

        $paginator = $builder
            ->paginate($options['per_page'])
            ->appends([
                ...$options['appends'],
                ...['per_page' => $options['per_page']]
            ]);

        return format_pagination($paginator);
    }
}

if (! function_exists('sign_url')) {
    /**
     * Signs a URL with a signature and optional expiration time.
     *
     * This method creates a signed URL by appending a signature to the provided URL.
     * The signature is generated using a cryptographic hash function and the application's key.
     *
     * @param \Stringable|string $url The URL to sign.
     * @param int|null $expiresIn The optional expiration time in seconds.
     *
     * @return string The signed URL.
     */
    function sign_url(\Stringable|string $url, ?int $expiresIn = null): string {
        $url = strval($url);
        $uri = Uri::of($url);

        if (intval($expiresIn) > 0) {
            $uri = $uri->withQuery(['expires' => Carbon::now()->addSeconds($expiresIn)->timestamp]);
        }

        $signature = hash_hmac('sha256',
            strval($uri),
            config('app.key')
        );

        $uri = $uri->withQuery(['signature' => $signature]);

        return strval($uri);
    }
}

if (! function_exists('verify_signed_url')) {
    /**
     * Verifies the signature of a signed URL and checks if it is valid or expired.
     *
     * This method validates the signature of a signed URL by comparing it with the expected signature.
     * It also checks if the URL has expired based on the optional expiration time.
     *
     * @param \Stringable|string $url The signed URL to verify.
     * @param bool $temporary Whether to check for temporary URL validity.
     *
     * @return SignedUrlState The state of the signed URL.
     */
    function verify_signed_url(\Stringable|string $url, $temporary = false): SignedUrlState {
        $url = strval($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return SignedUrlState::INVALID_URL;
        }

        $uri = Uri::of($url);

        $signature = strval($uri->query()->get("signature"));
        $expires = $uri->query()->integer("expires");

        if (!$signature) return SignedUrlState::INVALID_URL;

        if ($temporary && (!$expires ||  Carbon::now()->timestamp > $expires)) {
            return !$expires
                ? SignedUrlState::INVALID_URL
                : SignedUrlState::EXPIRED_URL;
        }

        $urlWithoutSignature = strval($uri->withoutQuery(['signature']));

        $expectedSignature = hash_hmac('sha256', $urlWithoutSignature, config('app.key'));

        return hash_equals($expectedSignature, $signature)
            ? SignedUrlState::VALID_URL
            : SignedUrlState::INVALID_URL;
    }
}

if (! function_exists('get_setting')) {
    /**
     * Retrieves a setting from the cache or database.
     *
     * This method checks the cache for a setting with the specified key.
     * If not found, it retrieves the setting from the database and caches it.
     *
     * @param string $key The key of the setting to retrieve.
     *
     * @return Setting|null The setting object if found, otherwise null.
     */
    function get_setting(string $key): ?Setting {
        $settings = Cache::remember('settings.all', now()->addMinutes(5), function () {
            return Setting::all()->keyBy('key');
        });

        $setting = $settings[$key] ?? null;

        return $setting;
    }
}

if (! function_exists('get_setting_value')) {
    /**
     * Retrieves the value of a setting from the cache or database.
     *
     * This method first checks the cache for a setting with the specified key.
     * If not found, it retrieves the setting from the database and caches it.
     *
     * @param string $key The key of the setting to retrieve.
     * @param mixed $default The default value to return if the setting is not found.
     *
     * @return mixed The value of the setting.
     */
    function get_setting_value(string $key, mixed $default = null): mixed {
        $setting = get_setting($key);

        if (!$setting) {
            return $default;
        }

        return SettingValueCast::castValue($setting->value, $setting->type);
    }
}

if (! function_exists('set_setting')) {
    /**
     * Sets the value of a setting in the database and caches it.
     *
     * This method creates or updates a setting with the specified key and value.
     * It also associates the setting with a setting group if provided.
     *
     * @param string $key The key of the setting to set.
     * @param mixed $value The value of the setting to set.
     * @param null|string|int|SettingGroup $group The group of the setting to set.
     *
     * @return Setting|null The setting object if created or updated, otherwise null.
     */
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
    /**
     * Clears the cache for all settings.
     *
     * This method invalidates the cache entry for all settings.
     * It ensures that the next time settings are accessed, they will be re-retrieved from the database.
     */
    function clear_settings_cache(): void {
        Cache::forget('settings.all');
    }
}
