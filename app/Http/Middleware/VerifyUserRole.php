<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleName): Response
    {
        // if the role name is not provided or the user is not authenticated or the user's role is not less than the role's level, throw a forbidden exception
        if (
            !$roleName ||
            !Auth::check() ||
            !(!!($role = Role::findByName($roleName)) && Auth::user()->role->level <= $role->level)
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, Response::$statusTexts[Response::HTTP_FORBIDDEN]);
        }

        return $next($request);
    }
}
