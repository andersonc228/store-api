<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use App\Shared\Domain\Assert\Assert;
use App\Shared\Domain\EventsTrait;
use DateTimeImmutable;

class User
{
    use EventsTrait;

    private string $id;
    private string $email;
    private string $password;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $email,
        string $password,
        DateTimeImmutable $createdAt,
    ) {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($email, 'email')->email()->maxLength(255)
            ->that($password, 'password')->notEmpty()
            ->verifyNow();

        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
