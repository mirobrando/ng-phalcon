<?php

namespace mirolabs\phalcon\Framework\Compile;

use Phalcon\Annotations\Adapter as Annotations;
use Phalcon\Config;

interface Plugin 
{
    function setConfig(Config $config);
    function getConfig();
    function parseFile(Annotations $adapter, $className, $module);
    function createCache($cacheDir);
}
