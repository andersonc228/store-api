<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ApiDoc;

use App\Api\Ui\Transformer\Common\Schema\ErrorInstance;
use App\Api\Ui\Transformer\Common\Schema\ErrorType;
use App\Product\Domain\Model\ProductStatus;

/**
 * Human-readable meaning of every value of the enums exposed in the API documentation.
 * Lives in the Ui layer (presentation concern) to keep the Domain enums pure. Surfaced as a
 * Markdown table in each enum's component-schema description by DescribedEnumModelDescriber.
 *
 * Keyed by enum class; every case of a listed enum MUST have an entry (enforced by a unit test).
 */
final class EnumDescriptions
{
    /** @return array<string, array<string, string>> */
    private function map(): array
    {
        return [
            ProductStatus::class => [
                ProductStatus::ACTIVE->value => 'The product is currently available for purchase.',
                ProductStatus::INACTIVE->value => 'The product is not currently available for purchase.',
                ProductStatus::DRAFT->value => 'The product is in a draft state and not yet available for purchase.',
            ],
            ErrorType::class => [
                ErrorType::BAD_REQUEST->value => 'The request is malformed or invalid.',
                ErrorType::METHOD_NOT_ALLOWED->value => 'The HTTP method is not allowed on the requested path.',
                ErrorType::INVALID->value => 'The provided credentials or token are invalid.',
                ErrorType::EXPIRED->value => 'The provided token has expired.',
                ErrorType::DISABLED->value => 'The resource is disabled and cannot be used.',
                ErrorType::FORBIDDEN->value => 'Authenticated but not allowed to access the resource.',
                ErrorType::NOT_FOUND->value => 'The requested resource does not exist.',
                ErrorType::MISSING_DATA->value => 'The resource lacks a required piece of data to execute the operation.',
                ErrorType::CONFLICT->value => 'The request conflicts with the current state of the resource.',
                ErrorType::IDENTIFIER_CONFLICT->value => 'A resource with the provided identifier already exists.',
                ErrorType::STATUS_CONFLICT->value => 'The operation is not allowed for the resource in its current status.',
                ErrorType::TYPE_CONFLICT->value => 'The operation is incompatible with the type of the resource.',
                ErrorType::VALIDATION->value => 'One or more request fields failed validation (see the errors field).',
                ErrorType::PAYLOAD_TOO_LARGE->value => 'The request body exceeds the maximum allowed size.',
                ErrorType::BAD_GATEWAY->value => 'An upstream service (e.g. the payment gateway) failed.',
                ErrorType::INTERNAL->value => 'An unexpected internal error occurred.',
            ],
            ErrorInstance::class => [
                ErrorInstance::REQUEST->value => 'The HTTP request itself.',
                ErrorInstance::AUTHENTICATION->value => 'Authentication/authorization.',
                ErrorInstance::USER->value => 'A user resource.',
                ErrorInstance::INTERNAL->value => 'An internal subsystem.',
                ErrorInstance::PRODUCT->value => 'A product resource.',
            ],
        ];
    }

    /** Markdown table (value -> meaning) for the given enum, or null if it is not documented. */
    public function table(string $enumClass): ?string
    {
        $descriptions = $this->map()[$enumClass] ?? null;
        if ($descriptions === null) {
            return null;
        }

        $rows = array_map(
            static fn (string $value, string $meaning): string => sprintf('| `%s` | %s |', $value, $meaning),
            array_keys($descriptions),
            array_values($descriptions),
        );

        return implode("\n", ['| Value | Meaning |', '| --- | --- |', ...$rows]);
    }

    /** @return array<string, array<string, string>> */
    public function all(): array
    {
        return $this->map();
    }
}
