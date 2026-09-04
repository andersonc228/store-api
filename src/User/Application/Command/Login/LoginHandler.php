<?php

declare(strict_types=1);

namespace App\User\Application\Command\Login;

use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Shared\Application\Bus\CommandHandler;
use App\Shared\Domain\Service\PasswordHasher;
use App\User\Domain\Exception\InvalidCredentialsException;
use App\User\Domain\Model\User;
use App\User\Domain\Model\UserRepository;
use App\User\Infrastructure\Security\User as SecurityUser;

readonly class LoginHandler implements CommandHandler
{
    public const int EXPIRATION_SECONDS = 86400;

    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private JWTTokenManagerInterface $apiJwtManager,
    ) {}
    public function __invoke(Login $command): LoginResponse
    {
        if (!$user = $this->userRepository->findByEmail($command->email)) {
            throw InvalidCredentialsException::fromEmail($command->email);
        }


        if (!$this->verifyWithUser($user, $command->password)) {
            throw InvalidCredentialsException::fromEmail($user->getEmail());
        }

        $currentTime = new DateTimeImmutable();
        $expirationTime = $currentTime->modify("+".self::EXPIRATION_SECONDS." seconds")->getTimestamp();

        return new LoginResponse(
            token: $this->apiJwtManager->create(SecurityUser::fromEntity($user)),
            expiresIn: $expirationTime,
        );
    }

    private function verifyWithUser(User $user, string $password): bool
    {
        return $this->passwordHasher->verify($user->getPassword(), $password);
    }
}
