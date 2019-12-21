<?php

namespace Amber\Container\Service;

class ServiceProperty
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \ReflectionProperty
     */
    public \ReflectionProperty $reflection;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $type;

    /**
     * The Service constructor.
     *
     * @param string $class The value of the service.
     */
    public function __construct(string $name, \ReflectionProperty $reflection)
    {
        $this->name = $name;
        $this->reflection = $reflection;
    }

    /**
     * Gets the property name.
     *
     * @return
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the property type.
     *
     * @return
     */
    public function getType()
    {
        if (isset($this->type)) {
            return $this->type;
        }

        return $this->type = $this->reflection->getType()->getName();
    }

    /**
     * Whether the property has type.
     *
     * @return
     */
    public function hasType(): bool
    {
        return $this->reflection->hasType();
    }

    /**
     * Sets a property argument.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setValue($value): self
    {
        $this->argument = $value;

        return $this;
    }

    /**
     * Wether a property argument is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return isset($this->argument);
    }

    /**
     * Gets a property argument.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->argument;
    }
}
