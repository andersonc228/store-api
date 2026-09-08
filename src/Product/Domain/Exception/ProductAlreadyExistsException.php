<?php

declare(strict_types=1);

namespace App\Product\Domain\Exception;

use DomainException;

class ProductAlreadyExistsException extends DomainException
{
    public static function fromReference(string $reference): self
    {
        return new self(sprintf('Product with reference "%s" already exists', $reference));
    }
}