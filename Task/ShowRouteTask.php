<?php

namespace mirolabs\phalcon\Task;

use mirolabs\phalcon\Framework\Task;
use mirolabs\phalcon\Task\Router\RouterDump;
use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Compile\Plugin\Route;
use mirolabs\phalcon\Framework\Container\Load;
use mirolabs\console\Output\Style;

class ShowRouteTask extends Task
{
    public function runAction($params)
    {
        $cacheDir = $params[0] .'/' . Module::COMMON_CACHE;
        $load = new Load($cacheDir);
        $router = new RouterDump();
        $load->execute(Route::CACHE_FILE , function() use ($router) {
            _loadRoutes($router);
        });

        $this->output()->writeln('');
        $this->output()->writelnStyle('Router:', new Style('white', 'blue', 'bold'));
        foreach($router->getRoutes() as $route) {
            $this->output()->write("path: \"");
            $this->output()->writeFormat($route['route'], 'info_bold');
            $this->output()->write("\" -> ");
            $this->output()->writeln(sprintf("%s/%s/%s", $route['data']['module'],$route['data']['controller'],$route['data']['action']));
        }

    }
}