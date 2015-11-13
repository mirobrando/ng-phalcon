<?php

namespace mirolabs\phalcon\Framework;

use Phalcon\Config\Adapter\Yaml;
use mirolabs\phalcon\Framework\App\App;
use mirolabs\phalcon\Framework\Compile\Parser;
use mirolabs\phalcon\Framework\Compile\Check;
use mirolabs\phalcon\Framework\Services\RegisterService;

class Application
{
    const ENVIRONMENT_DEV  = 'dev';
    const ENVIRONMENT_PROD = 'prod';

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var App
     */
    private $app;

    /**
     * @var array
     */
    private $modules;

    /**
     * @var Check
     */
    private $check;

    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * @param App $app
     * @param string $projectPath
     * @param string $environment
     */
    public function __construct(App $app, $projectPath, $environment = self::ENVIRONMENT_DEV)
    {
        $this->projectPath     = $projectPath;
        $this->environment     = $environment;
        $this->app             = $app;
        $yml                   = new Yaml($this->projectPath.'/config/modules.yml');
        $this->modules         = $yml->toArray();
        $this->check           = new Check($this->projectPath, $this->getModulesPath(), $this->environment);
        $this->registerService = new RegisterService();
    }

    public function run()
    {
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

    protected function createLogger()
    {
        Logger::$StartTime  = microtime(true);
        Logger::$ConfigPath = $this->projectPath.'/config/config.yml';
        Logger::getInstance()->debug("Start request");
    }

    protected function loadModules()
    {
        $this->app->registerModules($this->modules);
        Logger::getInstance()->debug("Register modules");
    }

    protected function compileAnnotations($di)
    {
        if ($this->check->isChangeConfiguration()) {
            $parser = new Parser($this->projectPath, $this->modules, $di->get('annotations'));
            $parser->execute();
        }
        Logger::getInstance()->debug("Complied annotations");
    }

    protected function loadServices($di)
    {
        $this->registerService
            ->setDependencyInjection($di)
            ->setProjectPath($this->projectPath)
            ->setModules($this->modules)
            ->setEnvironment($this->environment)
            ->setModulesPath($this->getModulesPath());

        $this->app->getContainer()->registerServices($this->registerService);
        Logger::getInstance()->debug("Loaded services");
    }

    protected function getModulesPath()
    {
        $result = [];
        foreach ($this->modules as $moduleName => $module) {
            preg_match('/([A-Za-z\/-]+)Module\.php/', $module['path'], $matches);
            $result[$moduleName] = $this->projectPath.$matches[1];
        }
        return $result;
    }
}