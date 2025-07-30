<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $recaptchaToken = $request->input('recaptcha_token');

        if (!!session()->token() && !$recaptchaToken) {
            abort(Response::HTTP_BAD_REQUEST, 'recaptcha_token missing');
        } else if (!!$recaptchaToken) {
            $response = Http::asForm()->post(config('services.recaptcha.verify_url'), [
                'secret' => config('services.recaptcha.secret'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            $result = (array) $response->json();

            abort_if(
                $response->failed() || data_get($result, 'success') !== true,
                Response::HTTP_BAD_REQUEST,
                'reCAPTCHA validation failed'
            );

            /*
            // Optional: score & action check for reCAPTCHA v3
            if (data_get($result, 'score') < 0.5 || data_get($result, 'action') !== 'your_action') {
                 return response()->json(['message' => 'Suspicious activity detected'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            */
        }

        return $next($request);
    }
}
