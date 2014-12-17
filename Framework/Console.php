<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\Container\Parser;
use mirolabs\phalcon\Framework\Services\Container\Cli;
use mirolabs\phalcon\Framework\Tasks\DefaultCommands;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\CLI\Console as ConsoleApp;
use mirolabs\phalcon\Framework\Services\Console as ConsoleDi;
use Symfony\Component\Yaml\Yaml;

class Console extends ConsoleApp
{
    /**
     * @var Yml
     */
    private $modules;

    private $projectPath;

    private $environment;

    private $args;

    public function __construct($args, $projectPath, $environment = Application::ENVIRONMENT_DEV)
    {
        $this->args = $args;
        $this->projectPath = $projectPath;
        $this->environment = $environment;
        parent::__construct();
    }

    protected function loadModules()
    {
        $this->modules = Yaml::parse(file_get_contents($this->projectPath. '/config/modules.yml'));
        $this->registerModules($this->modules);
    }

    protected function loadServices()
    {
        $registerService = new RegisterService();
        $registerService
            ->setProjectPath($this->projectPath)
            ->setModules($this->modules)
            ->setEnvironment($this->environment);
        $cli = new Cli();
        $cli->registerServices($registerService);
        $this->setDI($registerService->getDependencyInjection());
    }

    protected function getArguments()
    {

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

    public function main()
    {
        try {
            $this->loadModules();
            $this->loadServices();
            $this->handle($this->getArguments());
        } catch (Phalcon\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

    }


    protected function getTaskList()
    {
        return unserialize(
            file_get_contents($this->projectPath . '/' . Module::COMMON_CACHE . '/' . Parser::CACHE_TASKS)
        );
    }

    protected function getListTask($tasks)
    {
        $data['task'] = 'mirolabs\phalcon\Framework\Tasks\CommandList';
        $data['action'] = 'run';
        $data['params'] = ['tasks' => $tasks];
        return $data;
    }

    protected function getArgumentsFromTask($task)
    {
        $data['task'] = $task['class'];
        $data['action'] = $task['action'];
        $data['params'] = [];
        foreach ($task['params'] as $param) {
            if ($param['type'] == 'service') {
                $data['params'][] = $this->getDI()->get($param['name']);
            } else {
                $data['params'][] = $param['value'];
            }
        }

        return $data;
    }
}
