<?php

namespace mirolabs\phalcon\Framework;

use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;

abstract class Module implements ModuleDefinitionInterface
{
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
        $config = $di->get('config')->data;

        $view = new View();
        $view->setViewsDir($this->modulePath . '/views/');
        $volt = new Volt($view, $di);
        $volt->setOptions([
            'compiledPath' => $config['view']['compiledPath'],
            'compiledExtension' => $config['view']['compiledExtension'],
            'compiledSeparator' => $config['view']['compiledSeparator'],
            'stat' => $config['view']['stat'],
            'compileAlways' => $config['view']['compileAlways']
        ]);

        $volt->getCompiler()->addFunction('trans', function ($resolvedArgs, $exprArgs) use ($di) {
            return sprintf('$this->translation->__get(\'%s\')', $exprArgs[0]['expr']['value']);
        });

        $view->registerEngines([
            ".volt" => $volt
        ]);

        $view->setRenderLevel(View::LEVEL_ACTION_VIEW);


        $di->set('view', $view);
        $di->get('dispatcher')->setDefaultNamespace($this->moduleNamespace . "\controllers\\");
    }

} 