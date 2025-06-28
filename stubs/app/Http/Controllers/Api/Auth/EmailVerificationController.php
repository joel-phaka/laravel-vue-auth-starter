<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SignedUrlState;
use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Uri;

class EmailVerificationController extends Controller
{
    public function verifyEmail(Request $request): JsonResponse
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return response()
                ->json([
                    "message" => "Email already verified.",
                    "error_code" => "email_verification_already_verified"
                ])
                ->badRequest();
        }

        $uri = Uri::of($request->string('url'));
        $signedUrlState = Utils::verifySignedUrl($uri, true);

        if ($signedUrlState !== SignedUrlState::VALID_URL ||
            !hash_equals(strval($uri->query()->get('id')), strval(Auth::user()->getKey())) ||
            !hash_equals(strval($uri->query()->get('hash')), sha1(Auth::user()->getEmailForVerification()))
        ) {
            $error = match ($signedUrlState) {
                SignedUrlState::EXPIRED_URL => [
                    'message' => 'Expired email verification url',
                    'error_code' => 'email_verification_expired_url',
                ],
                default => [
                    'message' => 'Invalid email verification url',
                    'error_code' => 'email_verification_invalid_url',
                ]
            };

            return response()
                ->json($error)
                ->badRequest();
        }

        if (!Auth::user()->markEmailAsVerified()) {
            return response()
                ->json([
                    'message' => 'Email verification failed',
                    'error_code' => 'email_verification_failed',
                ])
                ->badRequest();
        }

        event(new Verified($request->user()));

        return response()->json(['message' => 'Email verified.']);
    }

    public function resendEmail(Request $request): JsonResponse
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return response()
                ->json([
                    "message" => "Email already verified.",
                    "error_code" => "email_verification_already_verified"
                ])
                ->badRequest();
        }

        Auth::user()->sendEmailVerificationNotification();

        return response()->json(['success' => true]);
    }
}
