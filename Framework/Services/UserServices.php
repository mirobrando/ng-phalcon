<?php

namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Application;
use mirolabs\phalcon\Framework\Container\Load;
use mirolabs\phalcon\Framework\Container\Parser;
use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\DI;

class UserServices implements Service
{

    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        $cacheDir = $registerService->getProjectPath() .'/' . Module::COMMON_CACHE;
        if ($registerService->getEnvironment() == Application::ENVIRONMENT_DEV) {
            $parser = new Parser(
                $registerService->getModulesPath(),
                $registerService->getProjectPath() . '/' . Module::CONFIG,
                $cacheDir,
                $registerService->getDependencyInjection()->get('annotations')
            );
            $parser->execute();
        }

        $load = new Load($cacheDir);
        $load->execute($registerService->getDependencyInjection());
        $config = $registerService->getDependencyInjection()->get('config');
        $config->set('projectPath', json_encode($registerService->getProjectPath()));
        $config->set('environment', json_encode($registerService->getEnvironment()));
    }
}
