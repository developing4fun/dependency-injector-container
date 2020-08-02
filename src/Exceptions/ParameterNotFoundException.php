<?php

declare(strict_types=1);

namespace Dev4Fun\Exceptions;

use Exception;
use function sprintf;

class ParameterNotFoundException extends Exception
{
    public static function forName(string $name): ParameterNotFoundException
    {
        return new self(
            sprintf('Could not find parameter called "%s"', $name)
        );
    }
}
