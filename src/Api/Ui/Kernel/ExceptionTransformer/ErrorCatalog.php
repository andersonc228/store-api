<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ExceptionTransformer;

use App\Api\Ui\Transformer\Common\Schema\ErrorInstance as I;
use App\Api\Ui\Transformer\Common\Schema\ErrorType as T;
use App\Product\Domain\Exception\ProductAlreadyExistsException;
use App\User\Domain\Exception\InvalidCredentialsException;
use App\User\Domain\Exception\UserNotFoundException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

/**
 * Single source of truth mapping every known exception to its API error response. Consumed by
 * ConfigExceptionTransformer (runtime responses) and by the documentation describer (error catalogue
 * table), so both always agree. The "general" entries cover errors produced outside this mapping
 * (validation, payload-too-large and the catch-all internal error).
 */
readonly class ErrorCatalog
{
    /** @var array<string, ErrorCatalogEntry> */
    private array $byException;

    public function __construct()
    {
        $this->byException = [
            NotFoundHttpException::class => new ErrorCatalogEntry(
                T::NOT_FOUND,
                404,
                'Path not found',
                'The requested path does not exist',
                I::REQUEST,
            ),
            MethodNotAllowedHttpException::class => new ErrorCatalogEntry(
                T::METHOD_NOT_ALLOWED,
                405,
                'Method not allowed',
                'The requested method is not allowed',
                I::REQUEST,
            ),
            BadRequestHttpException::class => new ErrorCatalogEntry(
                T::BAD_REQUEST,
                400,
                'Bad request',
                'A payload is required and must be a valid JSON',
                I::REQUEST,
            ),
            AccessDeniedException::class => new ErrorCatalogEntry(
                T::FORBIDDEN,
                403,
                'Access denied',
                'You do not have permission to access this resource',
                I::AUTHENTICATION,
            ),
            InvalidTokenException::class => new ErrorCatalogEntry(
                T::INVALID,
                401,
                'Token invalid',
                'The current JWT token is invalid',
                I::AUTHENTICATION,
            ),
            ExpiredTokenException::class => new ErrorCatalogEntry(
                T::EXPIRED,
                401,
                'Token expired',
                'The current JWT token is expired',
                I::AUTHENTICATION,
            ),
            ProductAlreadyExistsException::class => new ErrorCatalogEntry(
                T::IDENTIFIER_CONFLICT,
                409,
                'Product already exists',
                'The requested product already exists',
                I::PRODUCT,
            ),
            UserNotFoundException::class => new ErrorCatalogEntry(
                T::NOT_FOUND,
                404,
                'User not found',
                'The requested user does not exist',
                I::USER,
            ),
            InvalidCredentialsException::class => new ErrorCatalogEntry(
                T::NOT_FOUND,
                404,
                'User not found',
                'The requested user does not exist',
                I::USER,
            ),
        ];
    }

    public function forException(Throwable $exception): ?ErrorCatalogEntry
    {
        return $this->byException[$exception::class] ?? null;
    }

    /**
     * Every error the API can return: the mapped exceptions plus the general errors produced
     * outside the mapping (validation failures, payload too large and the catch-all internal error).
     *
     * @return list<ErrorCatalogEntry>
     */
    public function all(): array
    {
        return [
            ...array_values($this->byException),
            new ErrorCatalogEntry(
                T::VALIDATION,
                400,
                'Invalid parameters',
                'There are the following validation errors.',
                I::REQUEST,
            ),
            new ErrorCatalogEntry(
                T::PAYLOAD_TOO_LARGE,
                413,
                'Payload Too Large',
                'Request body exceeds the maximum allowed size.',
                I::REQUEST,
            ),
            new ErrorCatalogEntry(
                T::INTERNAL,
                500,
                'Internal Server Error',
                'An internal server error occurred. Try again later.',
                I::INTERNAL,
            ),
        ];
    }
}
