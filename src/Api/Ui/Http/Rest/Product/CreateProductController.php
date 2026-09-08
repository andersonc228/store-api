<?php

declare(strict_types=1);

namespace App\Api\Ui\Http\Rest\Product;

use App\Api\Ui\Transformer\Common\Schema\ValidationError;
use App\Product\Application\Command\CreateProduct\CreateProduct;
use App\Product\Domain\Model\ProductStatus;
use App\Shared\Application\Bus\CommandBus;
use App\Shared\Application\UuidGenerator;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

readonly class CreateProductController
{
    public function __construct(
        private CommandBus $commandBus,
        private UuidGenerator $uuidGenerator,
    ) {}

    #[OA\Tag(name: 'Products')]
    #[Route('/api/v1/products', name: 'api_products_create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ['reference', 'name', 'price', 'status'],
                properties: [
                    new OA\Property(property: 'reference', type: 'string', example: 'PRD-001'),
                    new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 19.99),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        example: 'active',
                        enum: [
                            ProductStatus::ACTIVE->value,
                            ProductStatus::DRAFT->value,
                        ]
                    ),
                    new OA\Property(property: 'description', type: 'string', example: 'Product description'),
                ],
                type: 'object',
            ),
        ],
    )]
    #[OA\Response(
        response: 201,
        description: 'Product created successfully'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid parameters',
        content: new OA\JsonContent(
            ref: new Model(type: ValidationError::class)
        )
    )]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        $command = new CreateProduct(
            id: $this->uuidGenerator->create(),
            reference: (string) ($payload['reference'] ?? ''),
            name: (string) ($payload['name'] ?? ''),
            price: (string) ($payload['price'] ?? ''),
            status: (string) ($payload['status'] ?? ''),
            description: isset($payload['description']) ? (string) $payload['description'] : null,
            createdAt: new DateTimeImmutable(),
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(status: JsonResponse::HTTP_CREATED);
    }
}
