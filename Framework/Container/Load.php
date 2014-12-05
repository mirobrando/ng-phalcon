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
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param DiInterface $dependencyInjection
     */
    public function execute($dependencyInjection)
    {
        require_once $this->cacheDir . '/' . Parser::CACHE_CONTAINER;
        _loadConfig($dependencyInjection);
        _loadServices($dependencyInjection);
    }
}
