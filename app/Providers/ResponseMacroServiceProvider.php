<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerHttpStatusMacros();
    }

    private function registerHttpStatusMacros(): void
    {
        $responseClasses = [
            Response::class,
            JsonResponse::class
        ];

        foreach ($responseClasses as $responseClass) {
            $responseClass::macro('ok', fn() => $this->setStatusCode(SymfonyResponse::HTTP_OK));
            $responseClass::macro('created', fn() => $this->setStatusCode(SymfonyResponse::HTTP_CREATED));
            $responseClass::macro('accepted', fn() => $this->setStatusCode(SymfonyResponse::HTTP_ACCEPTED));
            $responseClass::macro('noContent', fn() => $this->noContent());

            $responseClass::macro('notModified', fn() => $this->setStatusCode(SymfonyResponse::HTTP_NOT_MODIFIED));

            $responseClass::macro('temporaryRedirect', fn(string $url) => $this->redirectTo($url, SymfonyResponse::HTTP_TEMPORARY_REDIRECT));
            $responseClass::macro('permanentRedirect', fn(string $url) => $this->redirectTo($url, SymfonyResponse::HTTP_PERMANENTLY_REDIRECT));

            $responseClass::macro('badRequest', fn() => $this->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST));
            $responseClass::macro('unauthorized', fn() => $this->setStatusCode(SymfonyResponse::HTTP_UNAUTHORIZED));
            $responseClass::macro('paymentRequired', fn() => $this->setStatusCode(SymfonyResponse::HTTP_PAYMENT_REQUIRED));
            $responseClass::macro('forbidden', fn() => $this->setStatusCode(SymfonyResponse::HTTP_FORBIDDEN));
            $responseClass::macro('notFound', fn() => $this->setStatusCode(SymfonyResponse::HTTP_NOT_FOUND));
            $responseClass::macro('methodNotAllowed', fn() => $this->setStatusCode(SymfonyResponse::HTTP_METHOD_NOT_ALLOWED));
            $responseClass::macro('conflict', fn() => $this->setStatusCode(SymfonyResponse::HTTP_CONFLICT));
            $responseClass::macro('unprocessable', fn() => $this->setStatusCode(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY));
            $responseClass::macro('tooManyRequests', fn() => $this->setStatusCode(SymfonyResponse::HTTP_TOO_MANY_REQUESTS));
            $responseClass::macro('badGateway', fn() => $this->setStatusCode(SymfonyResponse::HTTP_BAD_GATEWAY));
            $responseClass::macro('serviceUnavailable', fn() => $this->setStatusCode(SymfonyResponse::HTTP_SERVICE_UNAVAILABLE));
            $responseClass::macro('gatewayTimeout', fn() => $this->setStatusCode(SymfonyResponse::HTTP_GATEWAY_TIMEOUT));

            $responseClass::macro('internalServerError', fn() => $this->setStatusCode(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
