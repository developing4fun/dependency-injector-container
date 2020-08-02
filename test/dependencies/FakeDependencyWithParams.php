<?php

declare(strict_types=1);

namespace Test\dependencies;

class FakeDependencyWithParams
{
    private FakeDependencyWithoutParams $fake_service;
    private string $fake_value;

    public function __construct(
        FakeDependencyWithoutParams $a_fake_service,
        string $a_fake_value
    ) {
        $this->fake_service = $a_fake_service;
        $this->fake_value = $a_fake_value;
    }

    public function fakeService(): FakeDependencyWithoutParams
    {
        return $this->fake_service;
    }

    public function fakeValue(): string
    {
        return $this->fake_value;
    }
}
