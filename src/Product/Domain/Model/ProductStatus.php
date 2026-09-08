<?php

namespace App\Product\Domain\Model;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
