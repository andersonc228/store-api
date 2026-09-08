<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use App\Api\Ui\Transformer\Common\Schema\ErrorInstance;
use App\Api\Ui\Transformer\Common\Schema\ErrorType;

readonly class ErrorCatalogEntry
{
    public function __construct(
        public ErrorType $type,
        public int $status,
        public string $title,
        public string $detail,
        public ErrorInstance $instance,
    ) {}
}
