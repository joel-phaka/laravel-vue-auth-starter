<?php


namespace App\Helpers;


use App\Enums\SignedUrlState;
use App\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Uri;
use InvalidArgumentException;

class Utils
{
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
    public static function formatPagination(LengthAwarePaginator $paginator): array
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
    public static function paginate(QueryBuilder|EloquentBuilder $builder, array $options = []): array
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

        return self::formatPagination($paginator);
    }

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
    public static function extractNonNullOrEmpty(array $arr): array
    {
        return array_filter($arr, function($v) {
            if (is_numeric($v) || is_array($v)) {
                return true;
            } else if (is_string($v)) {
                return !!trim($v);
            }

            return !empty($v);
        });
    }

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
    public static function baseUrl(?string $path = ''): string
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

        return $url;
    }

    public static function isLocalUrl(string $url): bool
    {
        $currentUrl = self::baseUrl();

        return parse_url($url, PHP_URL_SCHEME) === parse_url($currentUrl, PHP_URL_SCHEME) &&
               parse_url($url, PHP_URL_HOST) === parse_url($currentUrl, PHP_URL_HOST) &&
               parse_url($url, PHP_URL_PORT) === parse_url($currentUrl, PHP_URL_PORT);
    }

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
    public static function validateIsoDateTimeString(?string $isoDateTimeString, array $options = []): bool
    {
        $isTimeOptional = !!data_get($options, 'is_time_optional', true);
        $isMinuteOptional = !!data_get($options, 'is_minute_optional', true);
        $isSecondOptional = !!data_get($options, 'is_second_optional', true);

        $isoDateTimeRegex = "/^([1-2]\d{3}-((02-((0[1-9])|([1-2][0-9])))|(((0[469])|11)-((0[1-9])|([1-2][0-9])|30))|(((0[13578])|1[02])-((0[1-9])|([1-2][0-9])|3[01]))))";

        $isoDateTimeRegex .= "([T\s+]([01][0-9]|2[0-3])(\:([0-5][0-9]))" . ($isMinuteOptional ? "?" : "") . "(\:([0-5][0-9]))" . ($isSecondOptional ? "?" : "") . ")" . ($isTimeOptional ? "?" : "");

        $isoDateTimeRegex .= "$/";

        return !!preg_match($isoDateTimeRegex, $isoDateTimeString) && strtotime($isoDateTimeString) !== false;
    }

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
    public static function normaliseStartDateTime(?string $startDateTime): string
    {
        if (strtotime($startDateTime) === false) return '';

        return date('Y-m-d H:i:s', strtotime($startDateTime));
    }

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
    public static function normaliseEndDateTime(?string $endDateTime): string
    {
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
    public static function arrayInsertBefore(array $array, string $beforeKey, string $key, mixed $value): array
    {
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
    public static function arrayInsertAfter(array $array, string $afterKey, string $key, mixed $value): array
    {
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

    public static function enumNames(string $enumClass)
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("The given class is not a valid enum.");
        }

        return array_map(fn($case) => $case->name, $enumClass::cases());
    }

    public static function enumValues(string $enumClass)
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("The given class is not a valid enum.");
        }

        return array_map(fn($case) => $case->value, $enumClass::cases());
    }

    /**
     * Generate a unique username from an email address.
     *
     * This method takes an email address, extracts the username part (before the "@" symbol),
     * and ensures it is unique by checking against existing usernames in the database.
     * If the username exists, it appends or increments a numerical suffix to generate a unique username.
     *
     * @param string|null $email The email address to generate a username from.
     * @return bool|string Returns the generated username as a string, or false if the email is invalid.
     */
    public static function generateUsernameFromEmail(?string $email): bool|string
    {
        // Check if the provided email is valid
        if (!filter_var($email)) return false;

        // Extract the username part of the email
        $usernameOfEmail = explode("@", $email)[0];

        // Check if the username already exists in the database
        if (!User::where("username", $usernameOfEmail)->exists()) {
            return $usernameOfEmail; // Return it directly if unique
        }

        // Extract the base part of the username (without numerical suffix)
        $namePart = preg_match("/(.+[^0-9]+)[0-9]+$/", $usernameOfEmail, $matches)
            ? ($matches[1] ?? $usernameOfEmail)
            : $usernameOfEmail;

        // Fetch existing usernames matching the base part followed by numbers
        $numericalSuffixes = User::where("username", 'REGEXP', $namePart . '[0-9]*')
            ->select(["username"])
            ->whereNot('username', $namePart) // Exclude the exact base part
            ->orderBy('username', 'desc') // Sort by descending order for suffix comparison
            ->pluck('username') // Get the usernames
            ->map(fn($name) => str_replace($namePart, '', $name)) // Remove the base part
            ->toArray();

        // If no suffix exists, start with 1
        $suffix = $numericalSuffixes[0] ?? '';
        if (!$suffix) return $namePart . '1';

        // Generate a unique suffix
        do {
            // Break down the suffix into leading zeros and numeric parts
            preg_match('/^(0*)([1-9]+\d*)?$/', $suffix, $matches);
            $leadingZeros = $matches[1] ?? '';
            $trailingNumbers = $matches[2] ?? '';

            if ($leadingZeros !== '' && $trailingNumbers !== '') {
                // Case: Both leading zeros and numeric part exist
                $trailingNumbers = strval(intval($trailingNumbers) + 1); // Increment the numeric part

                // Adjust leading zeros if the new numeric part exceeds the suffix length
                if (strlen($trailingNumbers) >= strlen($suffix)) {
                    $leadingZeros = '';
                } else {
                    $lengthDiff = strlen($suffix) - strlen($trailingNumbers);
                    $leadingZeros = substr_replace($leadingZeros, '', $lengthDiff);
                }
            } else if ($leadingZeros !== '' && $trailingNumbers === '') {
                // Case: Only leading zeros exist
                if (strlen($leadingZeros) === 1) {
                    $leadingZeros = strval(1);
                } else {
                    $leadingZeros = substr($leadingZeros, 0, strlen($leadingZeros) - 1) . '1';
                }
            } else if ($leadingZeros === '' && $trailingNumbers !== '') {
                // Case: Only numeric part exists
                $trailingNumbers = strval(intval($trailingNumbers) + 1);
            }

            // Reconstruct the suffix
            $suffix = $leadingZeros . $trailingNumbers;

        } while ($suffix !== '' && in_array($suffix, $numericalSuffixes, true)); // Ensure uniqueness

        // Append the suffix to the base part and return the generated username
        $generatedUsername = $namePart . $suffix;

        return $generatedUsername;
    }

    public static function signUrl(\Stringable|string $url, ?int $expiresIn = null): string
    {
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

    public static function verifySignedUrl(\Stringable|string $url, $temporary = false): SignedUrlState
    {
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
