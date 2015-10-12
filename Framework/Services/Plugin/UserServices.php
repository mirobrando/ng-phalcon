<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use mirolabs\phalcon\Framework\Container\Load;
use mirolabs\phalcon\Framework\Compile\Plugin\Service as PluginService;


class UserServices implements Service {
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $cacheDir = $registerService->getProjectPath() .'/' . Module::COMMON_CACHE;
        $load = new Load($cacheDir);
        $load->execute(PluginService::CACHE_FILE, function() use ($registerService) {
            _loadServices($registerService->getDependencyInjection());
        });
    }
}
