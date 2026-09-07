<?php

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Domain\Model\ProductStatus;
use App\Shared\Application\Bus\Command;
use App\Shared\Domain\Assert\Assert;
use DateTimeImmutable;

class CreateProduct implements Command
{
    public function __construct(
        public string $id,
        public string $reference,
        public string $name,
        public string $price,
        public string $status,
        public ?string $description,
        public DateTimeImmutable $createdAt,
    ) {
        $validation = Assert::lazy()
            ->that($id, 'id')->notEmpty()->uuid()
            ->that($reference, 'reference')->notEmpty()
            ->that($name, 'name')->notEmpty()
            ->that($price, 'price')->notEmpty()->gt(0)
            ->that($status, 'status')->enum(ProductStatus::class);

        if ($description !== null) {
            $validation->that($description, 'description')->notEmpty()->maxLength(255);
        }

        $validation->verifyNow();
    }
}