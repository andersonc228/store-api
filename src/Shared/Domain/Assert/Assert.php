<?php

declare(strict_types=1);

namespace App\Shared\Domain\Assert;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use App\Shared\Common\Functional;
use Symfony\Component\Uid\Uuid;

class Assert
{
    public const string LABEL_REGEX = '/^MXL[A-Z0-9]{10}$/';
    private const string DATE_FORMAT = 'Y-m-d';

    public static function notEmpty(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (empty(is_string($value) ? trim($value) : $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" cannot be empty.',
                ),
            );
        }
    }

    public static function integerish(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match('/^-?\d+$/', $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be an integer.',
                ),
            );
        }
    }

    public static function numeric(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!is_numeric($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a number.',
                ),
            );
        }
    }

    public static function gt(
        string|int|float $value,
        string|int|float $limit,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value <= $limit) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must be greater than %s.', $limit),
                ),
            );
        }
    }

    public static function gte(
        string|int|float $value,
        string|int|float $limit,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value < $limit) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must be greater than or equal to %s.', $limit),
                ),
            );
        }
    }

    public static function lte(
        string|int|float $value,
        string|int|float $limit,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value > $limit) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must be less than or equal to %s.', $limit),
                ),
            );
        }
    }

    public static function email(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid email address.',
                ),
            );
        }
    }

    public static function uuid(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!Uuid::isValid($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid ULID.',
                ),
            );
        }
    }

    /** @param mixed[] $array */
    public static function inArray(
        mixed $value,
        array $array,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!in_array($value, $array, true)) {
            $allowed = Functional::map(static fn (mixed $item) => self::valueToString($item), $array);
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf(
                        'The {property} "{value}" must be one of the allowed values: [%s].',
                        implode(', ', $allowed),
                    ),
                ),
            );
        }
    }

    public static function enum(
        int|float|string $value,
        string $enumClass,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException(sprintf('The provided class "%s" must be a valid enum.', $enumClass));
        }
        /** @var array<int, int|float|string> $enumValues */
        $enumValues = array_column([$enumClass, 'cases'](), 'value');
        if (!in_array($value, $enumValues, true)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf(
                        'The {property} "{value}" must be one of the allowed enumerator values: [%s].',
                        implode(', ', $enumValues),
                    ),
                ),
            );
        }
    }

    public static function postalCode(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match('/^\d{5}$/', $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid postal code.',
                ),
            );
        }
    }

    public static function phone(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match('/^\+34(1\d)?\d{9}$/', $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid phone number.',
                ),
            );
        }
    }

    public static function time(
        string $value,
        ?string $message = null,
        ?string $property = null,
    ): void {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid time with format hh:mm.',
                ),
            );
        }
    }

    public static function label(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match(self::LABEL_REGEX, $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid label.',
                ),
            );
        }
    }

    public static function date(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        $date = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $value);
        if (!$date || $date->format(self::DATE_FORMAT) !== $value) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid date with format yyyy-mm-dd.',
                ),
            );
        }
    }

    public static function regex(
        string $value,
        string $pattern,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match($pattern, $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must match the pattern %s.', $pattern),
                ),
            );
        }
    }

    public static function minLength(
        string $value,
        int $length,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (mb_strlen($value) < $length) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must be at least %d characters long.', $length),
                ),
            );
        }
    }

    public static function maxLength(
        string $value,
        int $length,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (mb_strlen($value) > $length) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf('The {property} "{value}" must be at most %d characters long.', $length),
                ),
            );
        }
    }

    public static function eq(
        mixed $value,
        mixed $other,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value != $other) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf(
                        'The {property} "{value}" must be equal than expected "%s".',
                        self::valueToString($other),
                    ),
                ),
            );
        }
    }

    public static function same(
        mixed $value,
        mixed $other,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value !== $other) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? sprintf(
                        'The {property} "{value}" must be the same than expected "%s".',
                        self::valueToString($other),
                    ),
                ),
            );
        }
    }

    public static function true(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if ($value !== true) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be true.',
                ),
            );
        }
    }

    public static function notNull(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (is_null($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must not be null.',
                ),
            );
        }
    }

    public static function string(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!is_string($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a string.',
                ),
            );
        }
    }

    public static function integer(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!is_int($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be an integer.',
                ),
            );
        }
    }

    public static function float(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!is_int($value) && !is_float($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a float.',
                ),
            );
        }
    }

    public static function boolean(
        mixed $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!is_bool($value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a boolean.',
                ),
            );
        }
    }

    public static function url(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid URL.',
                ),
            );
        }
    }

    public static function path(
        string $value,
        ?string $property = null,
        ?string $message = null,
    ): void {
        if (!preg_match('#^(/[a-z0-9_-]+)+$#i', $value)) {
            throw AssertException::from(
                self::toAssertError(
                    $property,
                    self::valueToString($value),
                    $message ?? 'The {property} "{value}" must be a valid path.',
                ),
            );
        }
    }


    public static function lazy(): LazyAssert
    {
        return new LazyAssert();
    }

    private static function toAssertError(?string $property, string $value, string $messageFormat): AssertError
    {
        return new AssertError(
            $property ?? 'property',
            str_replace(['{property}', '{value}'], [$property ?? 'property', $value], $messageFormat),
        );
    }

    private static function valueToString(mixed $value): string
    {
        return match (true) {
            is_null($value) => '<NULL>',
            is_array($value) => '<ARRAY>',
            is_bool($value) => $value ? '<TRUE>' : '<FALSE>',
            is_scalar($value) => (string) $value,
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_object($value) => get_class($value),
            is_resource($value) => get_resource_type($value),
            default => 'not-parseable-to-string',
        };
    }
}
