<?php

namespace mirolabs\phalcon\Framework\Services\Container;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\DI\FactoryDefault;

class App
{
    protected $plugins = [
        'mirolabs\phalcon\Framework\Services\Listener',
        'mirolabs\phalcon\Framework\Services\UserServices',
        'mirolabs\phalcon\Framework\Services\Database',
        'mirolabs\phalcon\Framework\Services\Router',
        'mirolabs\phalcon\Framework\Services\Url',
        'mirolabs\phalcon\Framework\Services\Session\File',
        'mirolabs\phalcon\Framework\Services\Translation',
        'mirolabs\phalcon\Framework\Services\ManagementPath',
    ];


    /**
     * @param RegisterService $registerService
     */
    public function registerServices(RegisterService $registerService)
    {
        $registerService->setDependencyInjection(new FactoryDefault());
        $this->registerPlugins($registerService);
    }

    /**
     * @param RegisterService $registerService
     */
    protected function registerPlugins(RegisterService $registerService)
    {
        foreach ($this->plugins as $pluginClass) {
            $this->registerPlugins(new $pluginClass, $registerService);
        }
    }

    /**
     * @param Service $service
     * @param RegisterService $registerService
     */
    protected function registerPlugin(Service $service, RegisterService $registerService)
    {
        $service->register($registerService);
    }
}
