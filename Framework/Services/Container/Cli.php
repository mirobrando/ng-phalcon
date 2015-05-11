<?php

namespace mirolabs\phalcon\Framework\Services\Container;

use mirolabs\phalcon\Framework\TranslationCli;
use mirolabs\phalcon\Framework\Type\RegisterService;
use mirolabs\phalcon\Framework\View\ConsoleView;
use Phalcon\DI\FactoryDefault\CLI as CliDI;

class Cli extends App
{
    protected $plugins = [
        'mirolabs\phalcon\Framework\Services\Listener',
        'mirolabs\phalcon\Framework\Services\UserServices',
        'mirolabs\phalcon\Framework\Services\Database',
        'mirolabs\phalcon\Framework\Services\Translation',
    ];

    public function registerServices(RegisterService $registerService)
    {


        $di = new CliDi();
        $view = new ConsoleView();
        $view->setModuleName('cli');

        $di->set('view', $view);
        $di->set('session', new \mirolabs\phalcon\Framework\Services\Session\Cli());
        $di->set('translation', new TranslationCli());

        $registerService->setDependencyInjection($di);
        $this->registerPlugins($registerService);
    }
}
