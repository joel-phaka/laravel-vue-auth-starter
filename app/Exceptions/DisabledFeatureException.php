<?php

namespace App\Exceptions;

use App\Support\Feature;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DisabledFeatureException extends HttpException
{
    private array $featureNames;

    public function __construct(string|array $featureName)
    {
        $this->featureNames = Arr::wrap($featureName);

        parent::__construct(
            Response::HTTP_BAD_REQUEST,
            config('app.debug')
                ? $this->getDescription()
                : Response::$statusTexts[Response::HTTP_BAD_REQUEST]
        );
    }

    public function getFeatureNames(): array
    {
        return $this->featureNames;
    }

    public function getDescription(): string
    {
        return 'One or more features are disabled: ' . implode(', ', Feature::getDisabled($this->featureNames));
    }
}
