<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'level'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function checkRole(string|int|UserRole $userRole): bool
    {
        return $userRole instanceof UserRole
            ? !!self::findUserRole($userRole)
            : !!self::findByNameOrLevel($userRole);
    }

    public static function findByName(string $name)
    {
        return !!trim($name)
            ? static::firstWhere('name', strtolower($name))
            : null;
    }

    public static function findByLevel(int $level)
    {
        return !!$level
            ? static::firstWhere('level', $level)
            : null;
    }

    public static function findByNameOrLevel(string|int $arg): ?Role
    {
        return is_string($arg)
            ? self::findByName($arg)
            : self::findByLevel($arg);
    }

    public static function findUserRole(UserRole $userRole): ?Role
    {
        return static::where('name', strtolower($userRole->name))
            ->where('level', $userRole->value)
            ->first();
    }
}
