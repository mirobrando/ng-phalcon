<?php

namespace mirolabs\phalcon\Framework;

use Phalcon\Config\Adapter\Yaml;
use mirolabs\phalcon\Framework\App\App;
use mirolabs\phalcon\Framework\Compile\Parser;
use mirolabs\phalcon\Framework\Compile\Check;
use mirolabs\phalcon\Framework\Services\RegisterService;


class Application {
    
    const ENVIRONMENT_DEV = 'dev';
    const ENVIRONMENT_PROD = 'prod';

    private $projectPath;
    private $environment;
    private $app;
    private $modules;


    public function __construct(App $app, $projectPath, $environment = self::ENVIRONMENT_DEV) {
        $this->projectPath = $projectPath;
        $this->environment = $environment;
        $this->app = $app;
    }

    public function main() {
        $this->application->run();
    }

    public function getProjectPath() {
        return $this->projectPath;
    }

    public function getModules() {
        return $this->modules;
    }

        
    public function run() {
        try {
            $di = $this->app->getDI();
            $this->createLogger();
            $this->loadModules();
            $this->compileAnnotations($di);
            $this->loadServices($di);
            $this->app->execute();
            Logger::getInstance()->debug("Stop request");
        } catch (\Exception $e) {
            Logger::getInstance()->criticalException($e);
            $this->app->runException($e);
        }
    }
    
    
    protected function createLogger() {
        Logger::$StartTime = microtime(true);
        Logger::$ConfigPath = $this->projectPath. '/config/config.yml';
        Logger::getInstance()->debug("Start request");
    }
    
    protected function loadModules() {
        $config = new Yaml($this->projectPath. '/config/modules.yml');
        $this->modules = $config->toArray();
        $this->app->registerModules($this->modules);
        Logger::getInstance()->debug("Register modules");
    }
    
    protected function compileAnnotations($di) {
        $check = new Check($this->projectPath, $this->getModulesPath(), $this->environment);
        if ($check->isChangeConfiguration()) {
            $parser = new Parser($this->projectPath, $this->modules, $di->get('annotations'));
            $parser->execute();
        }
        Logger::getInstance()->debug("Complied annotations");
    }
    
    public function getModulesPath() {
        $result = [];
        foreach ($this->modules as $moduleName => $module) {
            preg_match('/([A-Za-z\/-]+)Module\.php/', $module['path'], $matches);
            $result[$moduleName] = $this->projectPath . $matches[1];
        }
        return $result;
    }
    
    protected function loadServices($di) {
        $registerService = new RegisterService();
        $registerService
            ->setDependencyInjection($di)
            ->setProjectPath($this->projectPath)
            ->setModules($this->modules)
            ->setEnvironment($this->environment)
            ->setModulesPath($this->getModulesPath());
        
        $this->app->getContainer()->registerServices($registerService);
        Logger::getInstance()->debug("Loaded services");
    }

}
