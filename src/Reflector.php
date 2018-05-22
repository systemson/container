<?php

namespace Amber\Container;

class Reflector
{
    /**
     * @var string The name of the class.
     */
    public $name;

    /**
     * @var object The ReflectionClass instance of the provided class.
     */
    public $reflection;

    /**
     * @var array An array of ReflectionProperty instances from the constructor properties.
     */
    public $properties;

    /**
     * @var array An array of the parameters for the constructor.
     */
    public $parameters;

    /**
     * @var array An array of ReflectionProperty instances for the injectable properties.
     */
    protected $injectables;

    /**
     * @var object An object containing the ReflectionMethod instance and the parameters
     *             for the constructor.
     */
    public $constructor;

    public function __construct($class)
    {
        $reflection = new \ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        $params = $constructor ? $constructor->getParameters() : [];

        /* Instantiate the ReflectionClass */
        $this->name = $class;
        $this->reflection = $reflection;
        $this->properties = $reflection->getProperties();
        $this->parameters = $params;
        $this->constructor = (object) [
            'reflection' => $constructor,
            'parameters' => $params,
        ];
    }

    /**
     * Instantiate the reflected class.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    public function newInstance($arguments = [])
    {
        if (!empty($arguments)) {
            $instance = $this->reflection->newInstanceArgs($arguments);
        } else {
            $instance = $this->reflection->newInstance();
        }

        return $instance;
    }

    /**
     * Find the injectable properties from the class.
     *
     * @return array An array of the injectable properties.
     */
    public function getInjectables()
    {
        if ($this->injectables !== null) {
            return $this->injectables;
        }

        foreach ($this->properties as $property) {
            if (preg_match(
                "'@inject\s(.*?)[\s\r\n|\r|\n]'",
                $property->getDocComment(),
                $match
            )) {
                $property->inject = $match[1];

                $this->injectables[] = $property;
            }
        }

        return $this->injectables ?? [];
    }
}
