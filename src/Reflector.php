<?php

namespace Amber\Container;

class Reflector
{
    public function __construct($class)
    {
        $reflection =  new \ReflectionClass($class);

        $method = $reflection->getConstructor();

        $params = $method ? $method->getParameters() : [];

        /* Instantiate the ReflectionClass */
        $this->name = $class;
        $this->reflection = $reflection;
        $this->properties = $reflection->getProperties();
        $this->injectables = $this->getInjectableProperties();
        $this->parameters = $params;
        $this->constructor = (object) [
            'reflection' => $method,
            'parameters' => $params,
        ];
    }

    public function newInstance($arguments = [])
    {
        if (!empty($arguments)) {
            $instance = $this->reflection->newInstanceArgs($arguments);
        } else {
            $instance = $this->reflection->newInstance();
        }

        return $instance;
    }

    public function getInjectableProperties()
    {
        foreach ($this->properties as $property) {
            if (preg_match("'@inject\s(.*?)[\r\n|\r|\n]'", $property->getDocComment(), $match)) {

                $property->inject = $match[1];

                $injectables[] = $property;
            }
        }

        return $injectables ?? [];
    }
}
