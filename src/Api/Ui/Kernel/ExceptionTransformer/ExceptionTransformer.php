<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

interface ExceptionTransformer
{
    public function match(Throwable $exception): bool;

    public function transform(Throwable $exception): JsonResponse;
}
