<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use mirolabs\phalcon\Framework\Container\Load;
use mirolabs\phalcon\Framework\Compile\Plugin\Listener;

class UserListeners implements Service {
    
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $cacheDir = $registerService->getProjectPath() .'/' . Module::COMMON_CACHE;
        $load = new Load($cacheDir);
        $load->execute(Listener::CACHE_FILE, function() use ($registerService) {
            _loadListeners($registerService->getDependencyInjection());
        });
    }
}
