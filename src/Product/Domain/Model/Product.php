<?php

declare(strict_types=1);

namespace App\Product\Domain\Model;

use App\Shared\Domain\Assert\Assert;
use App\Shared\Domain\EventsTrait;
use DateTimeImmutable;

class Product
{
    use EventsTrait;

    private string $id;
    private string $reference;
    private string $name;
    private string $price;
    private ProductStatus $status;
    private ?string $description;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $reference,
        string $name,
        string $price,
        ?ProductStatus $status,
        ?string $description,
        DateTimeImmutable $createdAt,
    ) {
        $validator = Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($reference, 'reference')->notEmpty()->maxLength(255)
            ->that($name, 'name')->notEmpty()->maxLength(255)
            ->that($price, 'price')->numeric()->gt('0')
            ->that($description, 'description')->maxLength(255);

        if ($description !== null) {
            $validator->that($description, 'description')->notEmpty()->maxLength(255);
        }

        $validator->verifyNow();

        $this->id = $id;
        $this->reference = strtoupper($reference);
        $this->name = $name;
        $this->price = $price;
        $this->status = $status ?? ProductStatus::ACTIVE;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    public static function create(
        string $id,
        string $name,
        string $price,
        string $reference,
        ?string $description
    ): self {
        return new self(
            id: $id,
            reference: $reference,
            name: $name,
            price: $price,
            status: ProductStatus::ACTIVE,
            description: $description,
            createdAt: new DateTimeImmutable()
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function reference(): string
    {
        return $this->reference;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): string
    {
        return $this->price;
    }

    public function status(): ProductStatus
    {
        return $this->status;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
