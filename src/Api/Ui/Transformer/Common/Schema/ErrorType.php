<?php

declare(strict_types=1);

namespace App\Api\Ui\Transformer\Common\Schema;

/**
 * Every error "type" the API can return. Single source of truth: referenced as
 * `ref: new Model(type: ErrorType::class)` from the Error schema (swagger-php expands it)
 * and used by the error catalogue, so the documented set can never drift from the real one.
 */
enum ErrorType: string
{
    case BAD_REQUEST = 'BAD_REQUEST';
    case METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
    case INVALID = 'INVALID';
    case EXPIRED = 'EXPIRED';
    case DISABLED = 'DISABLED';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';
    case MISSING_DATA = 'MISSING_DATA';
    case CONFLICT = 'CONFLICT';
    case IDENTIFIER_CONFLICT = 'IDENTIFIER_CONFLICT';
    case STATUS_CONFLICT = 'STATUS_CONFLICT';
    case TYPE_CONFLICT = 'TYPE_CONFLICT';
    case VALIDATION = 'VALIDATION';
    case PAYLOAD_TOO_LARGE = 'PAYLOAD_TOO_LARGE';
    case BAD_GATEWAY = 'BAD_GATEWAY';
    case INTERNAL = 'INTERNAL';
}
