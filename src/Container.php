<?php

declare(strict_types=1);

namespace Dev4Fun;

use Dev4Fun\Exceptions\ContainerException;
use Dev4Fun\Exceptions\DependencyNotFoundException;
use Dev4Fun\Exceptions\ParameterNotFoundException;
use Dev4Fun\Reference\DependencyReference;
use Dev4Fun\Reference\ParameterReference;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use function class_exists;
use function is_array;

class Container implements ContainerInterface
{
    private array $dependencies;
    private array $parameters;
    private array $dependencyStore;

    public function __construct(
        array $some_dependencies = [],
        array $some_parameters = []
    ) {
        $this->dependencies = $some_dependencies;
        $this->parameters = $some_parameters;
        $this->dependencyStore = [];
    }

    public function get($id)
    {
        $this->assertDependencyExists($id);

        if(!isset($this->dependencyStore[$id])) {
            $this->dependencyStore[$id] = $this->createDependency($id);
        }

        return $this->dependencyStore[$id];
    }

    private function assertDependencyExists(string $id): void
    {
        if (!$this->has($id)) {
            throw DependencyNotFoundException::forId($id);
        }
    }

    public function has($id)
    {
        return !isset($this->dependencies[$id]) ? $this->autoWired($id) : true;
    }

    private function autoWired(string $id): bool
    {
        try{
            $this->autoWire($id);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function autoWire(string $id): void
    {
        $reflected = new ReflectionClass($id);

        $dependencies = [];
        $constructor = $reflected->getConstructor();

        if ($constructor === null) {
            $this->dependencyStore[$id] = $reflected->newInstanceArgs($dependencies);
            return;
        }

        $parameters = $constructor->getParameters();

        foreach($parameters as $parameter) {
            if ($parameter->getClass() === null) {
                throw ContainerException::forMissingDependencyClass($parameter->getName());
            }

            $dependencies[] = $this->get($parameter->getClass()->getName());
        }

        $this->dependencyStore[$id] = $reflected->newInstanceArgs($dependencies);
    }

    private function createDependency(string $name)
    {
        $requested_class = $this->dependencies[$name];

        $this->assertClassKeyExists($name, $requested_class);
        $this->assertClassExists($name, $requested_class);
        $this->assertUnlockedDependency($name, $requested_class);

        $this->dependencies[$name]['lock'] = true;
        $arguments = isset($requested_class['arguments']) ? $this->resolveArguments($requested_class['arguments']): [];
        $reflected_class = new ReflectionClass($requested_class['class']);

        return $reflected_class->newInstanceArgs($arguments);
    }

    private function assertClassKeyExists(
        string $name,
        $requested_class
    ): void {
        if (!is_array($requested_class) || !isset($requested_class['class'])) {
            throw ContainerException::forMissingClassKey($name);
        }
    }

    private function assertClassExists(
        string $name,
        $requested_class
    ): void {
        if (!class_exists($requested_class['class'])) {
            throw ContainerException::forMissingDependencyClass($name, $requested_class['class']);
        }
    }

    private function assertUnlockedDependency(
        string $name,
        $requested_class
    ): void {
        if (isset($requested_class['lock'])) {
            throw ContainerException::forCircularReference($name);
        }
    }

    private function resolveArguments(array $argument_definitions)
    {
        $arguments = [];

        foreach ($argument_definitions as $argument_definition) {
            if ($argument_definition instanceof DependencyReference) {
                $argument_service_name = $argument_definition->name();
                $arguments[] = $this->get($argument_service_name);
                continue;
            }

            if ($argument_definition instanceof ParameterReference) {
                $argument_parameter_name = $argument_definition->name();
                $arguments[] = $this->getParameter($argument_parameter_name);
                continue;
            }

            $arguments[] = $argument_definition;
        }

        return $arguments;
    }

    private function getParameter(string $name): string
    {
        $keys = explode('.', $name);
        $context = $this->parameters;

        foreach($keys as $key) {
            $this->assertParameterExists($name, $context, $key);
            $context = $context[$key];
        }

        return (string) $context;
    }

    private function assertParameterExists(
        string $name,
        array $context,
        $key
    ): void {
        if (!isset($context[$key])) {
            throw ParameterNotFoundException::forName($name);
        }
    }
}
