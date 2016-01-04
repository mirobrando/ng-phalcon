<?php

namespace mirolabs\phalcon\Framework\Container;

use Phalcon\DiInterface;

/**
 * Class Load
 * @package mirolabs\phalcon\Framework\Container
 * @codeCoverageIgnore
 */
class Load
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir) {
        $this->cacheDir = $cacheDir;
    }


    public function execute($file, \Closure $cl) {
        require $this->cacheDir . $file;
        return $cl();
    }
}
