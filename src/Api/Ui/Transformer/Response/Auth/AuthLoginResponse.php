<?php

declare(strict_types=1);

namespace App\Api\Ui\Transformer\Response\Auth;

use JsonSerializable;
use App\User\Application\Command\Login\LoginResponse as Login;

readonly class AuthLoginResponse implements JsonSerializable
{
    public function __construct(private Login $response) {}

    /** @return array<mixed, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->response->token,
            'expires_in' => $this->response->expiresIn,
        ];
    }
}
