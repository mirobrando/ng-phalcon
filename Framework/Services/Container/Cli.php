<?php

namespace mirolabs\phalcon\Framework\Services\Container;

use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\DI\FactoryDefault\CLI as CliDI;

class Cli extends App
{
    protected $plugins = [
        'mirolabs\phalcon\Framework\Services\Listener',
        'mirolabs\phalcon\Framework\Services\UserServices',
        'mirolabs\phalcon\Framework\Services\Database',
        'mirolabs\phalcon\Framework\Services\Translation'
    ];

    public function registerServices(RegisterService $registerService)
    {
        $registerService->setDependencyInjection(new CliDI());
        $this->registerPlugins($registerService);
    }
}
