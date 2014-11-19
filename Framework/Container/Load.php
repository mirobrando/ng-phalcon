<?php

namespace mirolabs\phalcon\Framework\Container;


class Load
{
    /**
     * @var
     */
    private $cacheDir;

    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function execute($di)
    {
        require_once $this->cacheDir . '/' . Parser::CACHE_FILE;
        _loadConfig($di);
        _loadServices($di);
    }
}
