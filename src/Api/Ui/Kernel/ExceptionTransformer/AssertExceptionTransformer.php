<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use App\Api\Ui\Transformer\Common\Schema\Error;
use App\Shared\Common\Functional;
use App\Shared\Domain\Assert\AssertError;
use App\Shared\Domain\Assert\AssertException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

readonly class AssertExceptionTransformer implements ExceptionTransformer
{
    public function match(Throwable $exception): bool
    {
        return $exception instanceof AssertException;
    }

    /** @param AssertException $exception */
    public function transform(Throwable $exception): JsonResponse
    {
        return new JsonResponse(
            Error::validation(
                Functional::map(
                    static fn (array $errors) => Functional::map(
                        static fn (AssertError $error) => $error->message,
                        $errors,
                    ),
                    Functional::group(static fn (AssertError $error) => $error->property, $exception->errors()),
                ),
            ),
            JsonResponse::HTTP_BAD_REQUEST,
        );
    }
}
