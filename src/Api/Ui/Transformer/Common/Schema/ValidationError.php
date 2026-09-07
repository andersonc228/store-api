<?php

namespace App\Api\Ui\Transformer\Common\Schema;

use JsonSerializable;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    properties: [
        new Property(
            property: 'type',
            type: 'string',
            enum: [ErrorType::VALIDATION->value],
        ),
        new Property(property: 'title', type: 'string', example: 'Error title'),
        new Property(property: 'detail', type: 'string', example: 'Detailed error message'),
        new Property(property: 'instance', ref: new Model(type: ErrorInstance::class)),
        new Property(
            property: 'errors',
            properties: [
                new Property(property: '<property-name>', type: 'string', example: 'The "id" field is required.'),
            ],
            type: 'object',
            nullable: true,
        ),
    ],
    type: 'object',
)]
readonly class ValidationError implements JsonSerializable
{
    /** @param array<string, string[]> $parameters */
    public function __construct(
        private string $type,
        private string $title,
        private string $detail,
        private string $instance,
        private ?array $parameters = [],
    )
    {
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'type' => $this->type,
                'title' => $this->title,
                'detail' => $this->detail,
                'instance' => $this->instance,
                'errors' => $this->parameters,
            ],
        );
    }

    /** @param array<string, string[]> $parameters */
    public static function validation(array $parameters): self
    {
        return new self(
            'VALIDATION',
            'Invalid parameters',
            'There are the following validation errors.',
            'request',
            $parameters,
        );
    }
}