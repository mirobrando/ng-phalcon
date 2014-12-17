<?php
namespace mirolabs\phalcon\Framework\Type;

use Phalcon\DI;

class RegisterService
{
    /**
     * @var DI
     */
    private $dependencyInjection;

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var array
     */
    private $modules;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var array
     */
    private $modulesPath = array();

    /**
     * @return DI
     */
    public function getDependencyInjection()
    {
        return $this->dependencyInjection;
    }

    /**
     * @param DI $dependencyInjection
     * @return RegisterService
     */
    public function setDependencyInjection($dependencyInjection)
    {
        $this->dependencyInjection = $dependencyInjection;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * @param string $projectPath
     * @return RegisterService
     */
    public function setProjectPath($projectPath)
    {
        $this->projectPath = $projectPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param array $modules
     * @return RegisterService
     */
    public function setModules($modules)
    {
        $this->modules = $modules;

        foreach ($modules as $moduleName => $module) {
            preg_match('/([A-Za-z\/-]+)Module\.php/', $module['path'], $matches);
            $this->modulesPath[$moduleName] = $this->getProjectPath() . $matches[1];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     * @return RegisterService
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return array
     */
    public function getModulesPath()
    {
        return $this->modulesPath;
    }
}
