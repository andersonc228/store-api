<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use App\Api\Ui\Transformer\Common\Schema\Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

readonly class ConfigExceptionTransformer implements ExceptionTransformer
{
    public function __construct(private ErrorCatalog $catalog) {}

    public function match(Throwable $exception): bool
    {
        return null !== $this->catalog->forException($exception);
    }

    public function transform(Throwable $exception): JsonResponse
    {
        $entry = $this->catalog->forException($exception);
        if (null === $entry) {
            return new JsonResponse(Error::internal(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(
            new Error($entry->type->value, $entry->title, $entry->detail, $entry->instance->value),
            $entry->status,
        );
    }
}
