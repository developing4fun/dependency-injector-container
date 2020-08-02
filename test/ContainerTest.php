<?php

declare(strict_types=1);

namespace Test;

use Dev4Fun\Container;
use Dev4Fun\Exceptions\ContainerException;
use Dev4Fun\Exceptions\DependencyNotFoundException;
use Dev4Fun\Exceptions\ParameterNotFoundException;
use Dev4Fun\Reference\DependencyReference;
use Dev4Fun\Reference\ParameterReference;
use PHPUnit\Framework\TestCase;
use Test\dependencies\FakeDependencyAutoWired;
use Test\dependencies\FakeDependencyWithoutParams;
use Test\dependencies\FakeDependencyWithParams;

class ContainerTest extends TestCase
{
    protected Container $container;
    private const FAKE_VALUE = 'fake value';

    protected function setUp(): void
    {
        $dependencies = [
            'test.dependencies.fake_dependency_without_params' => [
                'class' => FakeDependencyWithoutParams::class,
                'arguments' => []
            ],
            'failed.no-class-key' => [],
            'failed.inexistent-class' => [
                'class' => '',
                'arguments' => []
            ],
            'test.dependencies.fake_dependency_with_params' => [
                'class' => FakeDependencyWithParams::class,
                'arguments' => [
                    new DependencyReference('test.dependencies.fake_dependency_without_params'),
                    new ParameterReference('value')
                ]
            ],
            'failed.circular-reference' => [
                'class' => FakeDependencyWithParams::class,
                'arguments' => [
                    new DependencyReference('failed.circular-reference'),
                    new ParameterReference('value')
                ]
            ],
            'failed.parameter-not-found' => [
                'class' => FakeDependencyWithParams::class,
                'arguments' => [
                    new DependencyReference('test.dependencies.fake_dependency_without_params'),
                    new ParameterReference('inexistent')
                ]
            ]
        ];

        $parameters = [
            'value' => self::FAKE_VALUE
        ];

        $this->container = new Container($dependencies, $parameters);
        parent::setUp();
    }

    public function testItShouldNotFindDependency(): void
    {
        $this->expectException(DependencyNotFoundException::class);
        $this->expectExceptionMessage('Could not find dependency with ID "id"');
        $this->container->get('id');
    }

    public function testItShouldGetDependencyWithoutParams(): void
    {
        $service = $this->container->get('test.dependencies.fake_dependency_without_params');
        $this->assertInstanceOf(FakeDependencyWithoutParams::class, $service);
    }

    public function testItThrowsExceptionWhenClassKeyIsMissing(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("'failed.no-class-key' dependency entry must be an array containing a 'class' key");
        $this->container->get('failed.no-class-key');
    }

    public function testItThrowsExceptionWhenClassDoesNotExists(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("'failed.inexistent-class' dependency class does not exist: ''");
        $this->container->get('failed.inexistent-class');
    }

    public function testItThrowsExceptionOnCircularReference(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("'failed.circular-reference' dependency contains a circular reference");
        $this->container->get('failed.circular-reference');
    }

    public function testItShouldGetDependencyWithParams(): void
    {
        /** @var FakeDependencyWithParams $service */
        $service = $this->container->get('test.dependencies.fake_dependency_with_params');
        $this->assertInstanceOf(FakeDependencyWithParams::class, $service);
        $this->assertInstanceOf(FakeDependencyWithoutParams::class, $service->fakeService());
        $this->assertEquals(self::FAKE_VALUE, $service->fakeValue());
    }

    public function testItThrowsExceptionWhenParameterNotFound(): void
    {
        $this->expectException(ParameterNotFoundException::class);
        $this->expectExceptionMessage('Could not find parameter called "inexistent"');
        $this->container->get('failed.parameter-not-found');
    }

    public function testShouldGetDependencyWithAutoWire(): void
    {
        /** @var FakeDependencyAutoWired $service */
        $service = $this->container->get(FakeDependencyAutoWired::class);
        $this->assertInstanceOf(FakeDependencyAutoWired::class, $service);
        $this->assertInstanceOf(FakeDependencyWithoutParams::class, $service->dependencyWithoutParams());
    }
}
