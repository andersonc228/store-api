<?php

declare(strict_types=1);

namespace App\Shared\Domain\Assert;

use TypeError;

class LazyChain
{
    private mixed $value;
    private ?string $property;
    private LazyAssert $lazyAssert;
    private bool $nullable;

    /** @var callable[] */
    private array $callables;

    public function __construct(mixed $value, ?string $property, LazyAssert $lazyAssert)
    {
        $this->value = $value;
        $this->property = $property;
        $this->lazyAssert = $lazyAssert;
        $this->nullable = false;
        $this->callables = [];
    }

    public function optional(): self
    {
        $this->nullable = true;
        return $this;
    }

    public function notEmpty(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::notEmpty($this->value, $this->property, $message));
    }

    public function integerish(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::integerish($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function numeric(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::numeric($this->value, $this->property, $message));
    }

    public function gt(int|float|string $limit, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::gt($this->value, $limit, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function gte(int|float|string $limit, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::gte($this->value, $limit, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function lte(int|float|string $limit, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::lte($this->value, $limit, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function email(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::email($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function uuid(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::uuid($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    /** @param mixed[] $array */
    public function inArray(array $array, ?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::inArray($this->value, $array, $this->property, $message));
    }

    public function enum(string $enumClass, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::enum($this->value, $enumClass, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function postalCode(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::postalCode($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function phone(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::phone($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function time(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::time($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function label(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::label($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function date(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::date($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function regex(string $pattern, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::regex($this->value, $pattern, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function minLength(int $length, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::minLength($this->value, $length, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function maxLength(int $length, ?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::maxLength($this->value, $length, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function eq(mixed $value, ?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::eq($this->value, $value, $this->property, $message));
    }

    public function same(mixed $value, ?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::same($this->value, $value, $this->property, $message));
    }

    public function true(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::true($this->value, $this->property, $message));
    }

    public function notNull(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::notNull($this->value, $this->property, $message));
    }

    public function string(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::string($this->value, $this->property, $message));
    }

    public function float(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::float($this->value, $this->property, $message));
    }

    public function integer(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::integer($this->value, $this->property, $message));
    }

    public function boolean(?string $message = null): self
    {
        return $this->addCallable(fn () => Assert::boolean($this->value, $this->property, $message));
    }

    public function url(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::url($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    public function path(?string $message = null): self
    {
        return $this->addCallable(
            fn () => Assert::path($this->value, $this->property, $message), // @phpstan-ignore-line
        );
    }

    private function addCallable(callable $callable): self
    {
        $this->callables[] = fn () => ($this->nullable && $this->value === null) || $callable();
        return $this;
    }

    public function that(mixed $value, ?string $property = null): self
    {
        return $this->lazyAssert->that($value, $property);
    }

    public function verifyNow(): void
    {
        $this->lazyAssert->verifyNow();
    }

    public function __invoke(): ?AssertError
    {
        foreach ($this->callables as $callable) {
            try {
                $callable();
            } catch (AssertException $ex) {
                $errors = $ex->errors();
                /** @var ?AssertError $firstError */
                $firstError = reset($errors);
                return $firstError ?: null;
            } catch (TypeError $err) {
                return new AssertError(
                    $this->property ?? 'property',
                    sprintf(
                        'The %s has an unexpected type "%s"',
                        $this->property ?? 'property',
                        get_debug_type($this->value),
                    ),
                );
            }
        }
        return null;
    }
}
