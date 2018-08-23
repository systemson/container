<?php

namespace Amber\Container\Config;

use Amber\Config\ConfigAwareInterface as BaseInterface;

interface ConfigAwareInterface extends BaseInterface
{
    const CACHE_DRIVER = 'file';

    const CACHE_SERVICES_NAME = 'injector_services';
}
