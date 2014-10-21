<?php

namespace mirolabs\phalcon\Framework\Tasks;

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
    public function _construct(CliDI $di, $modules, $projectPath)
    {
        $this->di = $di;
        $this->modules = $modules;
        $this->projectPath = $projectPath;
    }

    public function addTasks(array &$tasks)
    {
        foreach(get_class_methods($this) as $method) {
            if (preg_match('/^_add[a-zA-Z_]+/', $method)) {
                $this->$method($tasks);
            }
        }
    }

    public function _addCreateModule(&$tasks)
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

    public function _addInstallResources(&$tasks)
    {
        $tasks['installResources'] = [
            'class' => 'mirolabs\phalcon\Framework\Tasks\InstallResources',
            'action' => 'run',
            'description' => 'create symlink for resources of modules',
            'params' => [
                [
                    ['type' => 'parameter', 'value' => $this->projectPath],
                    ['type' => 'parameter', 'value' => $this->modules]
                ]
            ]
        ];
    }

}