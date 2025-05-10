<?php

declare(strict_types=1);

/**
 * Functional-style object helpers
 */

/**
 * Simulates Kotlin-style `apply`.
 *
 * @template T of object
 * @param T $object
 * @param callable(T): void $fn
 * @return T
 */
function apply(object $object, callable $fn): object {
    $fn($object);
    return $object;
}

/**
 * Simulates Kotlin-style `also`.
 *
 * Like `apply`, but focuses on the side effect and doesn't change the original object.
 *
 * @template T of object
 * @param T $object
 * @param callable(T): void $fn
 * @return T
 */
function also(object $object, callable $fn): object {
    $fn($object);
    return $object;
}

/**
 * Simulates Kotlin-style `let`.
 *
 * @template T of object
 * @template R
 * @param T $object
 * @param callable(T): R $fn
 * @return R
 */
function let(object $object, callable $fn): mixed {
    return $fn($object);
}
