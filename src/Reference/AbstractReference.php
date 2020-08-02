<?php

declare(strict_types=1);

namespace Dev4Fun\Reference;

class AbstractReference
{
    private string $name;

    public function __construct($a_name)
    {
        $this->name = $a_name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
