<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\View\RegisterView;
use mirolabs\phalcon\Framework\View\VoltCompiler;
use mirolabs\phalcon\Framework\Volt;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;

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
    public function registerAutoloaders()
    {
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $dependencyInjection
     */
    public function registerServices($dependencyInjection)
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
        return new RegisterView(new View(), $dependencyInjection);
    }
}
