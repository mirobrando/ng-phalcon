<?php

namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Symfony\Component\Yaml\Yaml;
use Phalcon\Mvc\Router as PhalconRouter;

class Router implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        $router = new PhalconRouter();
        $registerService->getDependencyInjection()->set('router', $router);
        foreach ($registerService->getModulesPath() as $module => $path) {
            $this->addRouteModule($router, $module, $path);
        }
    }

    /**
     * @param Router $router
     * @param string $module
     * @param string $path
     */
    private function addRouteModule($router, $module, $path)
    {
        $data = Yaml::parse(file_get_contents($path . 'config/route.yml'));
        if (is_array($data)) {
            foreach ($data as $r) {
                $router->add(
                    $r['pattern'],
                    [
                        'module' => $module,
                        'controller' => $r['option']['controller'],
                        'action' => $r['option']['action'],
                    ],
                    array_key_exists('method', $r) ? $r['method'] : null
                );
            }
        }
    }
}
