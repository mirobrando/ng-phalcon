<?php

namespace mirolabs\phalcon\Framework\App;

use Phalcon\CLI\Console as ConsoleApp;
use Phalcon\DI\FactoryDefault\CLI as CliDI;
use mirolabs\phalcon\Framework\Application;

class Console extends ConsoleApp implements App {

    /**
     * @param Application
     */
    private $application;
    
    /**
     * @var array
     */
    private $args;
    
    public function __construct($args, $projectPath, $environment = self::ENVIRONMENT_DEV) {
        $this->args = $args;
        $this->application = new Application($projectPath, $environment);
        parent::__construct();
    }
    
    public function main() {
        $this->application->run();
    }
    
    public function execute($di) {
        
    }

    public function runException(\Exception $ex) {
        echo $ex->getMessage() . "\n";
        echo sprintf("file: %s(%d)\n", $ex->getFile(), $ex->getLine());
        echo $ex->getTraceAsString();
    }

    public function getDI() {
        return new CliDi();
    }
    
    protected function getArguments() {

        $tasks = $this->getTaskList();
        $defaultCommands = new DefaultCommands($this->getDI(), $this->modules, $this->projectPath);
        $defaultCommands->addTasks($tasks);

        if (count($this->args) < 2) {
            return $this->getListTask($tasks);
        }
        if (!array_key_exists($this->args[1], $tasks)) {
            throw new Phalcon\Exception('command unavailable');
        }
        return $this->getArgumentsFromTask($tasks[$this->args[1]]);
    }


}
