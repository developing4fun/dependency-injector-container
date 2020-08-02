<?php

declare(strict_types=1);

namespace Dev4Fun\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use function sprintf;

final class DependencyNotFoundException extends Exception implements NotFoundExceptionInterface
{
    public static function forId(string $id): DependencyNotFoundException
    {
        return new self(
            sprintf('Could not find dependency with ID "%s"', $id)
        );
    }
}
