<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use Phalcon\Mvc\Router as PhalconRouter;
use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use mirolabs\phalcon\Framework\Compile\Plugin\Route;
use mirolabs\phalcon\Framework\Container\Load;

class Router implements Service {
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $router = new PhalconRouter();
        $registerService->getDependencyInjection()->set('router', $router);
        $cacheDir = $registerService->getProjectPath() .'/' . Module::COMMON_CACHE;
        $load = new Load($cacheDir);
        $load->execute(Route::CACHE_FILE , function() use ($router) {
            _loadRoutes($router);
        });
    }


}
