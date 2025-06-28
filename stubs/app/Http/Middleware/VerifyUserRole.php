<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Helpers\Utils;
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
        if (Auth::check()) {
            $hasAccess = false;

            if (!!$roleName &&
                !!($role = Role::findByName($roleName)) &&
                Auth::user()->role->level <= $role->level
            ) {
                $hasAccess = true;
            }

            if ($hasAccess) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => Response::$statusTexts[Response::HTTP_FORBIDDEN]], Response::HTTP_FORBIDDEN);
                } else {
                    throw new HttpException(Response::HTTP_FORBIDDEN, Response::$statusTexts[Response::HTTP_FORBIDDEN]);
                }
            }
        }

        return $next($request);
    }
}
