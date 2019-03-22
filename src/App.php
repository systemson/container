<?php

namespace Amber\Container;

use Amber\Container\Container;
use Amber\Utils\Implementations\AbstractWrapper;

class App extends AbstractWrapper
{
    protected static $accessor = Container::class;
    protected static $instance;
}
