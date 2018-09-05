<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;

class ServiceClass
{
    use Validator, ClassHandler, DependenciesHandler;

    /**
     *
     * @var string The name of the service.
     */
    public $key;

    /**
     *
     * @var mixed The value of the service.
     */
    public $value;

    /**
     *
     * @var string The value's type of the service.
     */
    public $type;

    /**
     * The Service constructor.
     *
     * @param string $key   The name of the service.
     * @param string $value The value of the service.
     */
    public function __construct($key, $value = null)
    {
        $this->key = $key;
        $this->value = $value ?? $key;
        $this->type = $this->getType($value);
    }
}
