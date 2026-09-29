<?php

declare(strict_types=1);
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Tests\Stub;

/**
 * Replacement for the win32service extension functions.
 *
 * The functions are declared in the namespace of the tested classes (see win32service_functions.php),
 * so PHP resolves them before the (absent) global ones.
 */
final class Win32ServiceFunctions
{
    /** @var array<string, mixed> */
    private static array $results = [];

    /** @var array<string, list<array<int, mixed>>> */
    private static array $calls = [];

    public static function reset(): void
    {
        self::$results = [];
        self::$calls = [];
    }

    /**
     * Define the value returned by the function, or the exception thrown if a Throwable is given.
     */
    public static function willReturn(string $function, mixed $result): void
    {
        self::$results[$function] = $result;
    }

    /**
     * @return list<array<int, mixed>>
     */
    public static function calls(string $function): array
    {
        return self::$calls[$function] ?? [];
    }

    public static function call(string $function, array $args): mixed
    {
        self::$calls[$function][] = $args;
        $result = self::$results[$function] ?? throw new \LogicException(sprintf('No result defined for %s()', $function));
        if ($result instanceof \Throwable) {
            throw $result;
        }

        return $result;
    }
}
