<?php

namespace Amber\Container;

use Amber\Utils\Implementations\AbstractWrapper;

/**
 * @deprecated
 */
class App extends AbstractWrapper
{
    /**
     * @var Container
     */
    protected static $accessor = Container::class;

    /**
     * @var mixed
     */
    protected static $instance;
}
