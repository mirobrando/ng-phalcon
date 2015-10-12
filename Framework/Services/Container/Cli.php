<?php

namespace mirolabs\phalcon\Framework\Services\Container;

//use mirolabs\phalcon\Framework\TranslationCli;
use mirolabs\phalcon\Framework\Services\RegisterService;
use mirolabs\phalcon\Framework\View\ConsoleView;
use mirolabs\phalcon\Framework\Services\Container;
use mirolabs\phalcon\Framework\Service;

class Cli implements Container
{
    protected $plugins = [
        'mirolabs\phalcon\Framework\Services\Plugin\Config',
        'mirolabs\phalcon\Framework\Services\Plugin\Listener',
        'mirolabs\phalcon\Framework\Services\Plugin\UserServices',
        'mirolabs\phalcon\Framework\Services\Plugin\Database',
        'mirolabs\phalcon\Framework\Services\Plugin\Translation',
    ];

    public function registerServices(RegisterService $registerService)
    {
        $di = $registerService->getDependencyInjection();
        $view = new ConsoleView();
        $view->setModuleName('cli');

        $di->set('view', $view);
        $di->set('session', new \mirolabs\phalcon\Framework\Services\Plugin\Session\Cli());
        //$di->set('translation', new TranslationCli());

        $this->registerPlugins($registerService);
    }
    
        /**
     * @param RegisterService $registerService
     */
    protected function registerPlugins(RegisterService $registerService) {
        foreach ($this->plugins as $pluginClass) {
            $this->registerPlugin(new $pluginClass, $registerService);
        }
    }

    /**
     * @param Service $service
     * @param RegisterService $registerService
     */
    protected function registerPlugin(Service $service, RegisterService $registerService) {
        $service->register($registerService);
    }
}
