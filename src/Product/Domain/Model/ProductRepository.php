<?php

declare(strict_types=1);

namespace App\Product\Domain\Model;

interface ProductRepository
{
    public function findByReference(string $reference): ?Product;

    public function save(Product ...$products): void;
}
