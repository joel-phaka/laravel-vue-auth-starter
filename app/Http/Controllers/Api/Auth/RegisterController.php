<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\AuthType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Events\UserRegistered;
use App\Models\Role;
use App\Models\User;
use App\Support\Auth\AuthEventData;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = new User([
            'role_id' => Role::findUserRole(UserRole::USER)?->id,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $user->saveOrFail();
        $user->refresh();

        Auth::login($user);

        $authEventData = new AuthEventData($user, AuthType::SESSION, session()->id());
        event(new UserRegistered($authEventData));

        return response()->json(['success' => true]);
    }
}
