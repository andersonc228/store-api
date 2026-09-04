<?php

declare(strict_types=1);

namespace App\Shared\Domain\Assert;

use App\Shared\Common\Functional;

class LazyAssert
{
    /** @var LazyChain[] */
    private array $chains;

    public function __construct()
    {
        $this->chains = [];
    }

    public function that(mixed $value, ?string $property = null): LazyChain
    {
        $chain = new LazyChain($value, $property, $this);
        $this->chains[] = $chain;

        return $chain;
    }

    public function verifyNow(): void
    {
        /** @var AssertError[] $errors */
        $errors = Functional::filter(
            static fn (mixed $value) => $value !== null,
            Functional::map(
                static fn (LazyChain $chain) => $chain->__invoke(),
                $this->chains,
            ),
        );
        if ($errors) {
            throw AssertException::from(...$errors);
        }
    }
}
