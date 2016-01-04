<?php

namespace mirolabs\phalcon\Framework\Services\Container;

use mirolabs\phalcon\Framework\Services\RegisterService;
use mirolabs\phalcon\Framework\Services\Container;
use mirolabs\phalcon\Framework\Service;

class Web implements Container {
    protected $plugins = [
        'mirolabs\phalcon\Framework\Services\Plugin\Config',
        'mirolabs\phalcon\Framework\Services\Plugin\Listener',
        'mirolabs\phalcon\Framework\Services\Plugin\UserServices',
        'mirolabs\phalcon\Framework\Services\Plugin\UserListeners',
        'mirolabs\phalcon\Framework\Services\Plugin\Database',
        'mirolabs\phalcon\Framework\Services\Plugin\Router',
        'mirolabs\phalcon\Framework\Services\Plugin\Url',
        'mirolabs\phalcon\Framework\Services\Plugin\Session\File',
        'mirolabs\phalcon\Framework\Services\Plugin\Translation',
        'mirolabs\phalcon\Framework\Services\Plugin\ManagementPath',
    ];


    /**
     * @param RegisterService $registerService
     */
    public function registerServices(RegisterService $registerService) {
        $this->registerPlugins($registerService);
    }

    /**
     * @param RegisterService $registerService
     */
    protected function registerPlugins(RegisterService $registerService) {
        foreach ($this->plugins as $pluginClass) {
            $this->registerPlugin(new $pluginClass, $registerService);
        }
    }

    /**
     * @param Service $service
     * @param RegisterService $registerService
     */
    protected function registerPlugin(Service $service, RegisterService $registerService) {
        $service->register($registerService);
    }
}
