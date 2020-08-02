<?php

declare(strict_types=1);

namespace Dev4Fun\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use function sprintf;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    public static function forMissingClassKey(string $dependency): ContainerException
    {
        return new self(
            sprintf("'%s' dependency entry must be an array containing a 'class' key", $dependency)
        );
    }

    public static function forMissingDependencyClass(string $dependency, string $class_name ): ContainerException
    {
        return new self(
            sprintf("'%s' dependency class does not exist: '%s'", $dependency, $class_name)
        );
    }

    public static function forCircularReference(string $dependency): ContainerException
    {
        return new self(
            sprintf("'%s' dependency contains a circular reference", $dependency)
        );
    }
}
