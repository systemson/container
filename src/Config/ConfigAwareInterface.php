<?php

namespace Amber\Container\Config;

use Amber\Config\ConfigAwareInterface as BaseInterface;

interface ConfigAwareInterface extends BaseInterface
{
    const PACKAGE_NAME = 'container';

    const CACHE_SERVICES_NAME = 'injector_services';
}
