<?php

declare(strict_types=1);

namespace App\Api\Ui\Transformer\Response\Auth;

use App\User\Application\Command\Login\LoginResponse as Login;
use JsonSerializable;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    properties: [
        new Property(property: 'token', type: 'string'),
        new Property(property: 'expires_in', type: 'integer'),
    ],
    type: 'object',
)]
readonly class AuthLoginResponse implements JsonSerializable
{
    public function __construct(
        private Login $response
    ) {}

    /** @return array<mixed, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->response->token,
            'expires_in' => $this->response->expiresIn,
        ];
    }
}
