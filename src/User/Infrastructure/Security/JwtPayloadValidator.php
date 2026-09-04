<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use App\User\Domain\Model\UserRepository;

readonly class JwtPayloadValidator
{
    public function __construct(private UserRepository $userRepository) {}

    #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_decoded')]
    public function onJwtDecoded(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();

        $user = $this->userRepository->findByEmail($payload['email']);

        if (!$user) {
            $event->markAsInvalid();
            return;
        }

        $event->setPayload([...$payload, 'id' => $user->getId()]);
    }
}
