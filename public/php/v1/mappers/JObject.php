<?php

class JObject
{
    /**
     * @throws ReflectionException
     */
    public function __set(string $name, $value): void
    {
        // Convert snake case to camel case
        $camelCaseVar = $this->toCamelCase($name);
        $reflectionClass = new ReflectionClass($this);
        $reflectionProperty = $reflectionClass->getProperty($camelCaseVar);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this, $value);
    }

    private function toCamelCase(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}