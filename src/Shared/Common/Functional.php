<?php

declare(strict_types=1);

namespace App\Shared\Common;

readonly class Functional
{
    /**
     * @template TKey of array-key
     * @template TValue
     * @template TNewKey of array-key
     * @param callable(TValue, TKey): TNewKey $fnKey
     * @param iterable<TKey, TValue> $coll
     * @return array<TNewKey&array-key, TValue>
     */
    public static function reindex(callable $fnKey, iterable $coll): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            $result[$fnKey($value, $key)] = $value;
        }
        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @template TReturn
     * @param callable(TValue, TKey): TReturn $fnValue
     * @param iterable<TKey, TValue> $coll
     * @return ($resetKey is true ? TReturn[] : array<TKey, TReturn>)
     */
    public static function map(callable $fnValue, iterable $coll, bool $resetKey = false): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            $result[$key] = $fnValue($value, $key);
        }
        return $resetKey ? array_values($result) : $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @template TResult
     * @template TNewKey of array-key
     * @param callable(TValue, TKey): TResult $fnValue
     * @param callable(TValue, TKey): TNewKey $fnKey
     * @param iterable<TKey, TValue> $coll
     * @return array<TNewKey, TResult>
     */
    public static function remap(callable $fnValue, callable $fnKey, iterable $coll): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            $result[$fnKey($value, $key)] = $fnValue($value, $key);
        }
        return $result;
    }

    /**
     * @template TKey
     * @template TValue
     * @param callable(TValue, TKey): mixed $fn
     * @param iterable<TKey,TValue> $coll
     */
    public static function each(callable $fn, iterable $coll): void
    {
        foreach ($coll as $key => $value) {
            $fn($value, $key);
        }
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @param callable(TValue, TKey): bool $fn
     * @param iterable<TKey, TValue> $coll
     */
    public static function any(callable $fn, iterable $coll): bool
    {
        foreach ($coll as $key => $value) {
            if ($fn($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @template TGroupKey of array-key
     * @param callable(TValue, TKey): TGroupKey $fnKey
     * @param iterable<TKey, TValue> $coll
     * @return array<TGroupKey, array<TValue>>
     */
    public static function group(callable $fnKey, iterable $coll): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            $result[$fnKey($value, $key)][] = $value;
        }
        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @template TResult
     * @template TKeyNew of array-key
     * @param callable(TValue, TKey): TResult $fnValue
     * @param callable(TValue, TKey): TKeyNew $fnKey
     * @param iterable<TValue> $coll
     * @return array<TKeyNew, array<TResult>>
     */
    public static function regroup(callable $fnValue, callable $fnKey, iterable $coll): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            $result[$fnKey($value, $key)][] = $fnValue($value, $key); // @phpstan-ignore-line
        }
        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @param callable(TValue, TValue): int $fnSortValue
     * @param array<TKey, TValue> $coll
     * @return ($resetKey is true ? TValue[] : array<TKey, TValue>)
     */
    public static function sort(callable $fnSortValue, array $coll, bool $resetKey = false): array
    {
        uasort($coll, $fnSortValue);
        return $resetKey ? array_values($coll) : $coll;
    }

    /**
     * @template TValue
     * @param iterable<TValue|iterable<TValue>> $coll
     * @return TValue[]
     */
    public static function flatten(iterable $coll): array
    {
        $result = [];
        foreach ($coll as $item) {
            if (is_iterable($item)) {
                $result = [...$result, ...self::flatten($item)];
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @param callable(TValue, TKey): bool $fn
     * @param iterable<TKey, TValue> $coll
     * @return ($resetKey is true ? TValue[] : array<TKey, TValue>)
     */
    public static function filter(callable $fn, iterable $coll, bool $resetKey = false): array
    {
        $result = [];
        foreach ($coll as $key => $value) {
            if ($fn($value, $key)) {
                $result[$key] = $value;
            }
        }
        return $resetKey ? array_values($result) : $result;
    }

    /**
     * @template TKey of array-key
     * @template TValue
     * @param callable(TValue, TKey): bool $fn
     * @param iterable<TKey, TValue> $coll
     * @return ?TValue
     */
    public static function first(callable $fn, iterable $coll): mixed
    {
        foreach ($coll as $key => $value) {
            if ($fn($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @template TValue
     * @param iterable<TValue> $coll
     * @return array<TValue>
     */
    public static function unique(iterable $coll): array
    {
        $result = [];
        foreach ($coll as $value) {
            if ($value !== null && !in_array($value, $result, true)) {
                $result[] = $value;
            }
        }
        return $result;
    }
}
