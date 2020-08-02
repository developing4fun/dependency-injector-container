<?php

namespace Test\dependencies;

class FakeDependencyAutoWired
{
    private FakeDependencyWithoutParams $dependencyWithoutParams;

    public function __construct(
        FakeDependencyWithoutParams $dependencyWithoutParams
    ) {
        $this->dependencyWithoutParams = $dependencyWithoutParams;
    }

    public function dependencyWithoutParams(): FakeDependencyWithoutParams
    {
        return $this->dependencyWithoutParams;
    }
}