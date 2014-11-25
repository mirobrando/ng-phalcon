<?php
namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Application;
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
    protected $modulesPath = array();

    private $projectPath;

    private $environment;

    public function __construct($projectPath, array $modules, $environment)
    {
        $this->environment = $environment;
        $this->projectPath = $projectPath;
        foreach ($modules as $moduleName => $module) {
            preg_match('/([A-Za-z\/-]+)Module\.php/', $module['path'], $matches);
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
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setListenerManager($dependencyInjection)
    {
        $eventsManager = new EventsManager();
        $dependencyInjection->set('listener', $eventsManager);
        $dependencyInjection->get('dispatcher')->setEventsManager($eventsManager);
    }


    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setDb($dependencyInjection)
    {
        if ($dependencyInjection->has('db')) {
            return;
        }
        $config = $dependencyInjection->get('config');
        $dependencyInjection->set('db', function () use ($config) {
            return new DbAdapter([
                'host' => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname' => $config->database->dbname
            ]);

        });
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setRouter($dependencyInjection)
    {
        $router = new Router();
        $dependencyInjection->set('router', $router);
        foreach ($this->modulesPath as $module => $path) {
            $this->addRouteModule($router, $module, $path);
        }
    }

    /**
     * @param Router $router
     * @param string $module
     * @param string $path
     */
    private function addRouteModule($router, $module, $path)
    {
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






    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setUrl($dependencyInjection)
    {
        $url = new UrlResolver();
        $url->setBaseUri('/');

        $dependencyInjection->set('url', $url);
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setSession($dependencyInjection)
    {
        $session = new SessionAdapter();
        $session->start();
        $dependencyInjection->set('session', $session);
    }

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setTranslation($dependencyInjection)
    {
        if ($dependencyInjection->has('translation')) {
            return;
        }
        $config = $dependencyInjection->get('config');
        $lang = $config->get('default.lang');
        if (empty($lang)) {
            $lang = 'en';
        }
        $dependencyInjection->set('translation', [
            'className' => 'mirolabs\phalcon\Framework\Translation',
            'arguments' => [
                ['type' => 'service', 'name' => 'dispatcher'],
                ['type' => 'parameter', 'value' => $this->modulesPath],
                ['type' => 'parameter', 'value' => $lang]
            ]
        ]);
    }


    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function registerUserServices($dependencyInjection)
    {
        $cacheDir = $this->projectPath .'/' . Module::COMMON_CACHE;
        $check = new Check($this->modulesPath, $cacheDir);
        if ($this->environment == Application::ENVIRONMENT_DEV || !$check->isCacheExist()) {
            if ($check->isChangeConfiguration()) {
                $parser = new Parser(
                    $this->modulesPath,
                    $this->projectPath . '/' . Module::CONFIG,
                    $cacheDir
                );
                $parser->execute();
            }
        }

        $load = new Load($cacheDir);
        $load->execute($dependencyInjection);

        $dependencyInjection->get('config')->set('projectPath', json_encode($this->projectPath));
        $dependencyInjection->get('config')->set('environment', $this->dev);
    }
}
