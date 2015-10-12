<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\View\RegisterView;
//use mirolabs\phalcon\Framework\View\View;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;

abstract class Module implements ModuleDefinitionInterface
{
    const CONFIG = 'config/config.yml';
    const SERVICE = 'config/services.yml';
    const COMMON = 'common';
    const COMMON_CACHE =  'common/cache';

    protected $modulePath = '/';

    protected $moduleNamespace = '\\';

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(DiInterface $dependencyInjector = NULL)
    {
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $dependencyInjection
     */
    public function registerServices(DiInterface $dependencyInjection)
    {
        $registerView = $this->getRegisterView($dependencyInjection);
        $registerView->register(
            $dependencyInjection->get('router')->getModuleName(),
            $this->modulePath
        );
        $dependencyInjection->get('dispatcher')->setDefaultNamespace($this->moduleNamespace . "\controllers\\");
    }

    /**
     * @param $dependencyInjection
     * @return RegisterView
     */
    protected function getRegisterView($dependencyInjection)
    {
        return new RegisterView(new \Phalcon\Mvc\View(), $dependencyInjection);
    }
}
