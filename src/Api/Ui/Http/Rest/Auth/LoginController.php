<?php

declare(strict_types=1);

namespace App\Api\Ui\Http\Rest\Auth;

use App\Api\Ui\Transformer\Common\Schema\Error;
use App\Api\Ui\Transformer\Common\Schema\ValidationError;
use App\Api\Ui\Transformer\Response\Auth\AuthLoginResponse as ResponseLogin;
use App\Shared\Application\Bus\CommandBus;
use App\User\Application\Command\Login\Login;
use App\User\Application\Command\Login\LoginResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class LoginController
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

    #[OA\Tag(name: 'Login')]
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(security: [])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'some.user'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'some_password_123'),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(response: '200', description: 'Login successful', content: new Model(type: ResponseLogin::class))]
    #[OA\Response(response: '400', description: 'Invalid payload', content: new Model(type: ValidationError::class))]
    #[OA\Response(response: '401', description: 'Bad credentials', content: new Model(type: Error::class))]
    #[OA\Response(response: '403', description: 'User blocked', content: new Model(type: Error::class))]
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
