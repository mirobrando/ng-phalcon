<?php

namespace mirolabs\phalcon\Framework;


use mirolabs\phalcon\Framework\Services\Standard;
use Symfony\Component\Yaml\Yaml;

class Application extends \Phalcon\Mvc\Application
{

    /**
     * @var Yml
     */
    private $modules;

    private $projectPath;

    private $dev;

    public function __construct($projectPath, $dev = true)
    {
        $this->projectPath = $projectPath;
        $this->dev = $dev;
        parent::__construct();
    }

    protected function loadModules()
    {
        $this->modules = Yaml::parse($this->projectPath. '/config/modules.yml');
        $this->registerModules($this->modules);
    }

    protected function loadServices()
    {
        $services = new Standard($this->projectPath, $this->modules, $this->dev);
        $di = $services->createContainer();
        $services->setListenerManager($di);
        $services->registerUserServices($di);
        $services->setDb($di);
        $services->setRouter($di);
        $services->setUrl($di);
        $services->setSession($di);
        $services->setTranslation($di);
        $this->setDI($di);
    }

    public function main()
    {
        try {
            $this->loadModules();
            $this->loadServices();
            echo $this->handle()->getContent();
        } catch (Phalcon\Mvc\Dispatcher\Exception $e) {
            $response = new \Phalcon\Http\Response();
            $response->setStatusCode(400, 'Bad Request');
            $response->send();
        } catch (Phalcon\Exception $e) {
            echo $e->getMessage();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }
}
