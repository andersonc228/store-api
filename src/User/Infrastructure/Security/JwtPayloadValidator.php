<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\Model\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

readonly class JwtPayloadValidator
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_decoded')]
    public function onJwtDecoded(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();

        if (!isset($payload['email']) || !is_string($payload['email'])) {
            $event->markAsInvalid();
            return;
        }

        $user = $this->userRepository->findByEmail($payload['email']);

        if (!$user) {
            $event->markAsInvalid();
            return;
        }

        $event->setPayload([
            ...$payload,
            'id' => $user->getId(),
        ]);
    }
}
