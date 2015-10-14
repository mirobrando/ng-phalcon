<?php

namespace mirolabs\phalcon\Task;

use Phalcon\DI\FactoryDefault\CLI as CliDI;

class DefaultCommands
{
    /**
     * @var CliDI $di
     */
    private $di;

    /**
     * @var array modules;
     */
    private $modules;

    /**
     * @var string $projectPath
     */
    private $projectPath;

    /**
     * @param CliDI $di
     * @param array $modules
     * @param string string $projectPath
     */
    public function __construct(CliDI $di, $modules, $projectPath)
    {
        $this->di = $di;
        $this->modules = $modules;
        $this->projectPath = $projectPath;
    }

    public function addTasks(array &$tasks)
    {
        foreach (get_class_methods($this) as $method) {
            if (preg_match('/^set[a-zA-Z_]+/', $method)) {
                $this->$method($tasks);
            }
        }
    }

    protected function setCreateModule(&$tasks)
    {
        $tasks['createModule'] = [
            'class' => 'mirolabs\phalcon\Framework\Tasks\CreateModule',
            'action' => 'run',
            'description' => 'create module in project',
            'params' => [
                ['type' => 'parameter', 'value' => $this->projectPath]
            ]
        ];
    }

    protected function setInstallResources(&$tasks)
    {
        $tasks['installResources'] = [
            'class' => 'mirolabs\phalcon\Task\InstallResources',
            'action' => 'run',
            'description' => 'create symlink for resources of modules',
            'params' => [
                ['type' => 'parameter', 'value' => $this->projectPath],
                ['type' => 'parameter', 'value' => $this->modules]
            ]
        ];
    }


    protected function setCreateController(&$tasks)
    {
        $tasks['createController'] = [
            'class' => 'mirolabs\phalcon\Framework\Tasks\Module\CreateController',
            'action' => 'run',
            'description' => 'create controller in project',
            'params' => [
                ['type' => 'parameter', 'value' => $this->projectPath],
            ]
        ];
    }

    protected function setCreateModelFromDB(&$tasks)
    {
        $tasks['createModelFromDB'] = [
            'class' => 'mirolabs\phalcon\Framework\Tasks\Module\CreateModelFromDB',
            'action' => 'run',
            'description' => 'create model from database',
            'params' => [
                ['type' => 'parameter', 'value' => $this->projectPath],
            ]
        ];
    }
}
