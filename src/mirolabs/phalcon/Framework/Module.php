<?php

namespace mirolabs\phalcon\Framework;

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
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        $config = $di->get('config');

        $view = new View();
        $view->setViewsDir($this->modulePath . '/views/');
        $volt = new Volt($view, $di);
        $volt->setOptions([
            'compiledPath' => $config->view->compiledPath,
            'compiledExtension' => $config->view->compiledExtension,
            'compiledSeparator' => $config->view->compiledSeparator,
            'stat' => $config->view->stat,
            'compileAlways' => $config->view->compileAlways,
            'commonView' => $config->projectPath . 'common/views/'
        ]);

        $volt->getCompiler()->addFilter('raw', function($resolvedArgs, $exprArgs) {
            return 'html_entity_decode(' . $resolvedArgs . ')';
        });

        $volt->getCompiler()->addFunction('lang', function () use ($di) {
            return '$this->translation->getLang()';
        });

        $volt->getCompiler()->addFunction('trans', function ($resolvedArgs, $exprArgs) use ($di) {
            return sprintf('$this->translation->__get(\'%s\')', $exprArgs[0]['expr']['value']);
        });

        $volt->getCompiler()->addFunction('ng', function($input) {
            return '"{{".' . $input . '."}}"';
        });

        $view->registerEngines([
            ".volt" => $volt
        ]);

        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);


        $di->set('view', $view);
        $di->get('dispatcher')->setDefaultNamespace($this->moduleNamespace . "\controllers\\");
    }

} 