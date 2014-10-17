<?php
namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Container\Check;
use mirolabs\phalcon\Framework\Container\Load;
use mirolabs\phalcon\Framework\Container\Parser;
use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Services;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Events\Manager as EventsManager;
use Symfony\Component\Yaml\Yaml;

class Standard implements Services
{
    private $modulesPath = array();

    private $projectPath;

    private  $dev;

    public function __construct($projectPath, array $modules, $dev = true)
    {
        $this->dev = $dev;
        $this->projectPath = $projectPath;
        foreach($modules as $moduleName => $module) {
            preg_match('/([A-Za-z\/-]+)Module\.php/',$module['path'], $matches);
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
    public function setListenerManager($di)
    {
        $eventsManager = new EventsManager();
        $di->set('listener', $eventsManager);
        $di->get('dispatcher')->setEventsManager($eventsManager);
    }


    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setDb($di)
    {
        if($di->has('db')) {
            return;
        }
        $config = $di->get('config');
        $di->set('db', function() use ($config) {
            return new DbAdapter([
                'host' => $config->database['host'],
                'username' => $config->database['username'],
                'password' => $config->database['password'],
                'dbname' => $config->database['name']
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
            $data = Yaml::parse(file_get_contents($path . 'config/route.yml'));
            if (is_array($data)) {
                foreach ($data as $r) {
                    $router->add(
                        $r['pattern'],
                        [
                            'module' => $module,
                            'controller' => $r['option']['controller'],
                            'action' => $r['option']['action'],
                        ],
                        array_key_exists('method', $r) ? $r['method'] : null
                    );
                }
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
        if($di->has('translation')) {
            return;
        }

        $di->set('translation', [
            'className' => 'mirolabs\phalcon\Framework\Translation',
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
    public function registerUserServices($di)
    {
        $cacheDir = $this->projectPath .'/' . Module::COMMON_CACHE;
        $check = new Check($this->modulesPath, $cacheDir);
        if ($this->dev || !$check->isCacheExist()) {
            if($check->isChangeConfiguration()) {
                $parser = new Parser(
                    $this->modulesPath,
                    $this->projectPath . '/' . Module::CONFIG,
                    $cacheDir
                );
                $parser->execute();
            }
        }

        $load = new Load($cacheDir);
        $load->execute($di);
        $di->get('config')->set('projectPath', json_encode($this->projectPath));
    }
} 