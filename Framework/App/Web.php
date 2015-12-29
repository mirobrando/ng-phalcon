<?php
namespace mirolabs\phalcon\Framework\App;

use Phalcon\Mvc\Application as PhalconApp;
use Phalcon\DI\FactoryDefault;
use mirolabs\phalcon\Framework\Application;
use mirolabs\phalcon\Framework\Services\Container\Web as WebContainer;

class Web extends PhalconApp implements App {
    
    /**
     * @param Application
     */
    private $application;
    
    private $di;
    
    public function __construct($projectPath, $environment = Application::ENVIRONMENT_DEV) {
        $this->application = new Application($this, $projectPath, $environment);
        parent::__construct();
    }
    
    public function main() {
        $this->di = new FactoryDefault();
        $this->setDI($this->di);
        $this->application->run();
    }
    
    public function execute() {
        echo $this->handle()->getContent();
    }
    
    public function getDI() {
        return $this->di;
    }
    
    public function runException(\Exception $ex) {
        $response = new \Phalcon\Http\Response();
        $response->setStatusCode(400, 'Bad Request');
        $response->send();
    }

    public function setModules($modules) {
        parent::registerModules($modules);
    }
    
    public function getContainer() {
        return new WebContainer();
    }

    public function getUri() {
        return $_SERVER['REQUEST_URI'];
    }
}
