<?php

declare(strict_types=1);

namespace App\Shared\Domain\Assert;

use App\Shared\Common\Functional;
use InvalidArgumentException;

class AssertException extends InvalidArgumentException
{
    /** @var AssertError[] */
    private array $errors;

    private function __construct(AssertError ...$errors)
    {
        parent::__construct(self::formatMessage(...$errors));
        $this->errors = $errors;
    }

    /** @return AssertError[] */
    public function errors(): array
    {
        return $this->errors;
    }

    public static function from(AssertError ...$errors): self
    {
        return new self(...$errors);
    }

    private static function formatMessage(AssertError ...$errors): string
    {
        $numErrors = count($errors);
        if ($numErrors === 1) {
            /** @var AssertError $first */
            $first = reset($errors);
            return $first->message;
        }

        $messages = Functional::map(
            static fn (AssertError $error, int $key) => sprintf(
                '%d) %s: %s',
                $key + 1,
                $error->property,
                $error->message,
            ),
            array_values($errors),
        );

        return implode(
            PHP_EOL,
            [
                sprintf('The following %d assertions failed:', $numErrors),
                ...$messages,
            ],
        );
    }
}
