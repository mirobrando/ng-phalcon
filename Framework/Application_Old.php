<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\Compile\Parser;
use mirolabs\phalcon\Framework\Services\Container\App;
use mirolabs\phalcon\Framework\Services\Standard;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\DI\FactoryDefault;

class Application extends \Phalcon\Mvc\Application
{
    const ENVIRONMENT_DEV = 'dev';
    const ENVIRONMENT_PROD = 'prod';


    /**
     * @var Yml
     */
    private $modules;

    private $projectPath;

    private $environment;

    public function __construct($projectPath, $environment = self::ENVIRONMENT_DEV)
    {
        $this->projectPath = $projectPath;
        $this->environment = $environment;
        parent::__construct();
    }

    protected function createLogger()
    {
        Logger::$StartTime = microtime(true);
        Logger::$ConfigPath = $this->projectPath. '/config/config.yml';
        Logger::getInstance()->debug("Start request");
    }
    
    protected function loadModules()
    {
        $config = new Yaml($this->projectPath. '/config/modules.yml');
        $this->modules = $config->toArray();
        $this->registerModules($this->modules);
        Logger::getInstance()->debug("Register modules");
    }

    protected function compileAnnotations($di)
    {
        $parser = new Parser($this->projectPath, $this->modules, $di->get('annotations'));
        
        //todo check
        $parser->execute();
                
        Logger::getInstance()->debug("Complied annotations");
    }


    protected function loadServices()
    {
        $registerService = new RegisterService();
        $registerService
            ->setProjectPath($this->projectPath)
            ->setModules($this->modules)
            ->setEnvironment($this->environment);
        
        $app = new App();
        $app->registerServices($registerService);
        $this->setDI($registerService->getDependencyInjection());
        Logger::getInstance()->debug("Loaded services");
    }

    public function main()
    {
        try {
            $di = new FactoryDefault();
            $this->createLogger();
            $this->loadModules();
            $this->compileAnnotations($di);
            $this->loadServices();
            echo $this->handle()->getContent();
            Logger::getInstance()->debug("Stop request");
        } catch (Phalcon\Mvc\Dispatcher\Exception $e) {
            Logger::getInstance()->criticalException($e);
            $response = new \Phalcon\Http\Response();
            $response->setStatusCode(400, 'Bad Request');
            $response->send();
        } catch (Phalcon\Exception $e) {
            Logger::getInstance()->criticalException($e);
        } catch (PDOException $e) {
            Logger::getInstance()->criticalException($e);
        } catch (\Exception $e) {
            Logger::getInstance()->criticalException($e);
        }
    }
}
