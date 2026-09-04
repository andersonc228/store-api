<?php

declare(strict_types=1);

namespace App\Api\Ui\Http\Rest\Auth;

use App\Shared\Application\Bus\CommandBus;
use App\User\Application\Command\Login\Login;
use App\User\Application\Command\Login\LoginResponse;
use App\Api\Ui\Transformer\Response\Auth\AuthLoginResponse as ResponseLogin;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class LoginController
{
    public function __construct(private CommandBus $commandBus) {}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var array{email: ?string, password: ?string} $payload */
        $payload = $request->toArray();
        /** @var LoginResponse $loginResponse */
        $loginResponse = $this->commandBus->dispatch(
            new Login(
                (string) ($payload['email'] ?? ''),
                (string) ($payload['password'] ?? ''),
            ),
        );
        return new JsonResponse(new ResponseLogin($loginResponse), Response::HTTP_OK);
    }
}
