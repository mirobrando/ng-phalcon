<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use Phalcon\Config\Adapter\Yaml;
use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;

class Config implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $config = new Yaml($registerService->getProjectPath() . '/config/config.yml');
        $registerService->getDependencyInjection()->set('config' , $config);
        
        $config->projectPath = $registerService->getProjectPath();
    }
}
