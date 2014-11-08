<?php

namespace mirolabs\phalcon\Framework\Services;

use Phalcon\DI\FactoryDefault\CLI as CliDI;

class Console extends Standard
{
    /**
     * create container dependency injector
     * @return \Phalcon\DI\FactoryDefault\CLI
     */
    public function createContainer()
    {
        return new CliDI();
    }


    public function getTaskList()
    {

    }


} 