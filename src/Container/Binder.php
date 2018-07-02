<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Service\Service;
use Amber\Validator\Validator;

/**
 * Class to handle the Container's binder.
 */
abstract class Binder extends SimpleBinder
{
    use BinderExtras;
}
