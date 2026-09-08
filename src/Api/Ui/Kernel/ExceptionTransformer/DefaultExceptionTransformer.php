<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use App\Api\Ui\Transformer\Common\Schema\Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

readonly class DefaultExceptionTransformer implements ExceptionTransformer
{
    public function __construct() {}

    public function match(Throwable $exception): bool
    {
        return true;
    }

    public function transform(Throwable $exception): JsonResponse
    {
        return new JsonResponse(Error::internal(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
