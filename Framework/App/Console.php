<?php

namespace mirolabs\phalcon\Framework\App;

use Phalcon\CLI\Console as ConsoleApp;
use Phalcon\DI\FactoryDefault\CLI as CliDI;
use mirolabs\phalcon\Framework\Application;
use mirolabs\phalcon\Framework\Services\Container\Cli as CliContainer;
use mirolabs\phalcon\Task\DefaultCommands;

class Console extends ConsoleApp implements App {

    /**
     * @param Application
     */
    private $application;
    
    /**
     * @var array
     */
    private $args;
    
    private $di;
    
    public function __construct($args, $projectPath, $environment = Application::ENVIRONMENT_DEV) {
        $this->args = $args;
        $this->application = new Application($this, $projectPath, $environment);
        parent::__construct();
    }
    
    public function main() {
        $this->di = new CliDI();
        $this->setDI($this->di);
        $this->application->run();
    }
    
    public function execute() {
        $this->handle($this->getArguments());
    }

    public function runException(\Exception $ex) {
        echo $ex->getMessage() . "\n";
        echo sprintf("file: %s(%d)\n", $ex->getFile(), $ex->getLine());
        echo $ex->getTraceAsString();
    }

    public function getDI() {
        return $this->di;
    }
    
    public function getContainer() {
        return new CliContainer();
    }
    
    public function setModules($modules) {
        parent::registerModules($modules);
    }
    protected function getArguments() {

        //$tasks = $this->getTaskList();
        $tasks = [];
        $defaultCommands = new DefaultCommands($this->getDI(), 
                $this->application->getModules(), $this->application->getProjectPath());
        $defaultCommands->addTasks($tasks);

        if (count($this->args) < 2) {
            return $this->getListTask($tasks);
        }
        if (!array_key_exists($this->args[1], $tasks)) {
            throw new Phalcon\Exception('command unavailable');
        }
        return $this->getArgumentsFromTask($tasks[$this->args[1]]);
    }
    
    protected function getListTask($tasks) {
        $data['task'] = 'mirolabs\phalcon\Task\CommandList';
        $data['action'] = 'run';
        $data['params'] = ['tasks' => $tasks];
        return $data;
    }

}
