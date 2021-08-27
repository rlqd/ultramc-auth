<?php

namespace Tests\Helpers;


use PHPUnit\Framework\AssertionFailedError;

trait TSingletonMock
{
    private array $mockedSingletons = [];

    protected function mockSingleton(string $className, object $instance)
    {
        if (!($instance instanceof $className)) {
            throw new AssertionFailedError('Failed to mock singleton ' . $className . ' with object of type ' . get_class($instance));
        }
        $this->DbInstanceProp = new \ReflectionProperty($className, 'instance');
        $this->DbInstanceProp->setAccessible(true);
        $this->DbInstanceProp->setValue($instance);
        $this->mockedSingletons[] = $className;
    }

    protected function resetSingleton(string $className)
    {
        $this->DbInstanceProp = new \ReflectionProperty($className, 'instance');
        $this->DbInstanceProp->setAccessible(true);
        $this->DbInstanceProp->setValue(null);
    }

    protected function resetMockedSingletons()
    {
        foreach (array_unique($this->mockedSingletons) as $className) {
            $this->resetSingleton($className);
        }
    }
}
