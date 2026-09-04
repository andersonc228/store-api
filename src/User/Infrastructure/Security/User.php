<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use App\User\Domain\Model\User as DomainUser;

final readonly class User implements JWTUserInterface
{
    public function __construct(
        private string $id,
        private string $email,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @param array{id:string,roles:string[]} $payload */
    public static function createFromPayload($username, array $payload): JWTUserInterface
    {
        return new self((string) $payload['id'], (string) $username);
    }

    public static function fromEntity(DomainUser $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),

        );
    }

    public function getRoles(): array
    {
       return ['ROLE_USER'];
    }
}
