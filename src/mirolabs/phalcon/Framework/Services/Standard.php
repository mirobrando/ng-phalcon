<?php
namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Services;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Symfony\Component\Yaml\Yaml;

class Standard implements Services
{
    private $modulesPath = array();

    private $projectPath;

    public function __construct($projectPath, array $modules)
    {
        $this->projectPath = $projectPath;
        foreach($modules as $moduleName => $module) {
            preg_match('/([a-z\/-]+)Module\.php/',$module['path'], $matches);
            $this->modulesPath[$moduleName] = $projectPath . $matches[1];
        }
    }

    /**
     * create container dependency injector
     * @return \Phalcon\DI\FactoryDefault
     */
    public function createContainer()
    {
        return new FactoryDefault();
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setConfig($di)
    {
        $config = new \stdClass();
        $config->data = Yaml::parse(file_get_contents($this->projectPath . 'config/config.yml'));

        $di->set(
            'config',
            $config
        );
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setDb($di)
    {
        $config = $di->get('config')->data;
        $di->set('db', function() use ($config) {
            return new DbAdapter([
                'host' => $config['database']['host'],
                'username' => $config['database']['username'],
                'password' => $config['database']['password'],
                'dbname' => $config['database']['name']
            ]);

        });
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setRouter($di)
    {
        $router = new Router();
        foreach ($this->modulesPath as $module=>$path) {
            foreach (Yaml::parse(file_get_contents($path . 'config/route.yml')) as $r) {
                $router->add(
                    $r['pattern'],
                    [
                        'module' => $module,
                        'controller' => $r['option']['controller'],
                        'action' => $r['option']['action'],
                    ],
                    array_key_exists('method', $r)? $r['method'] : null
                );
            }
        }

        $di->set('router', $router);
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setUrl($di)
    {
        $url = new UrlResolver();
        $url->setBaseUri('/');

        $di->set('url', $url);
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setSession($di)
    {
        $session = new SessionAdapter();
        $session->start();
        $di->set('session', $session);
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setTranslation($di)
    {
        $di->set('translation', [
            'className' => 'Framework\Translation',
            'arguments' => [
                ['type' => 'service', 'name' => 'dispatcher'],
                ['type' => 'parameter', 'value' => $this->modulesPath]
            ]
        ]);
    }


    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function registerOtherServices($di)
    {
        foreach ($this->modulesPath as $module => $path) {
            $services = Yaml::parse(file_get_contents($path . 'config/services.yml'));
            if (!is_null($services['services'])) {
                foreach ($services['services'] as $serviceName => $params) {
                    $di->set($serviceName, $params);
                }
            }
        }
    }
} 