<?php

declare(strict_types=1);

namespace App\Api\Ui\Transformer\Common\Schema;

/**
 * The subject ("instance") an error refers to. Single source of truth: referenced as
 * `ref: new Model(type: ErrorInstance::class)` from the Error schema and used by the error catalogue.
 */
enum ErrorInstance: string
{
    case REQUEST = 'request';
    case AUTHENTICATION = 'authentication';
    case USER = 'user';
    case INTERNAL = 'internal';
    case PRODUCT = 'product';
}
