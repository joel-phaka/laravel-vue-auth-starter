<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'role_id' => Role::findUserRole(UserRole::USER)?->id,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if (!$user) {
            return response()
                ->json(['message' => 'Failed to create user account.'])
                ->badRequest();
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['success' => true]);
    }
}
