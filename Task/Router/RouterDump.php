<?php

namespace mirolabs\phalcon\Task\Router;

class RouterDump
{
    private $routes = [];

    public function add($route, $data)
    {
        $this->routes[] = ['route'=>$route, 'data'=>$data];
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}