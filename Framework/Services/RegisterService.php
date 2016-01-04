<?php
namespace mirolabs\phalcon\Framework\Services;

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
    public function getDependencyInjection() {
        return $this->dependencyInjection;
    }

    /**
     * @param DI $dependencyInjection
     * @return RegisterService
     */
    public function setDependencyInjection($dependencyInjection) {
        $this->dependencyInjection = $dependencyInjection;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectPath() {
        return $this->projectPath;
    }

    /**
     * @param string $projectPath
     * @return RegisterService
     */
    public function setProjectPath($projectPath) {
        $this->projectPath = $projectPath;

        return $this;
    }

    /**
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }

    /**
     * @param array $modules
     * @return RegisterService
     */
    public function setModules($modules) {
        $this->modules = $modules;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * @param string $environment
     * @return RegisterService
     */
    public function setEnvironment($environment) {
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
    
    /**
     * @param array $modulesPath
     * @return RegisterService
     */
    public function setModulesPath($modulesPath) {
        $this->modulesPath = $modulesPath;

        return $this;
    }


}
