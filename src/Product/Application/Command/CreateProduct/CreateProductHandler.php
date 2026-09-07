<?php

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Domain\Model\Product;
use App\Product\Domain\Model\ProductRepository;
use App\Product\Domain\Model\ProductStatus;
use App\Shared\Application\Bus\CommandHandler;

final readonly class CreateProductHandler implements CommandHandler
{
    public function __construct(
        private ProductRepository $repository
    ) {}

    public function __invoke(CreateProduct $command): void
    {
        $product = new Product(
            id: $command->id,
            reference: $command->reference,
            name: $command->name,
            price: $command->price,
            status: ProductStatus::from($command->status),
            description: $command->description,
            createdAt: $command->createdAt
        );

        $this->repository->save($product);
    }
}