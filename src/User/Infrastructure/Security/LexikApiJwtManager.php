<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\Model\User;
use App\User\Domain\Service\ApiJwtManager;
use App\User\Infrastructure\Security\User as SecurityUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class LexikApiJwtManager implements ApiJwtManager
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {}

    public function create(User $user): string
    {
        return $this->jwtManager->create(SecurityUser::fromEntity($user));
    }
}
