<?php

namespace mirolabs\phalcon\Framework\Container;


use mirolabs\phalcon\Framework\Container\Parser\Factory;
use mirolabs\phalcon\Framework\Container\Parser\Listener;
use mirolabs\phalcon\Framework\Container\Parser\Standard;
use mirolabs\phalcon\Framework\Module;
use Symfony\Component\Yaml\Yaml;

class Parser implements Output
{
    const CACHE_FILE = 'container.php';
    const CACHE_TASKS = '.task.log';
    /**
     * @var string
     */
    private $modulesPath;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $servicesData = [];

    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @param string $modulesPath
     * @param string $configPath
     * @param string $cacheDir
     */
    public function __construct($modulesPath, $configPath, $cacheDir)
    {
        $this->modulesPath = $modulesPath;
        $this->configPath = $configPath;
        $this->cacheDir = $cacheDir;
    }

    public function execute()
    {
        foreach ($this->modulesPath as $modulePath) {
            $serviceFile = $modulePath . '/' . Module::SERVICE;
            $data = Yaml::parse($serviceFile);
            $this->parseParam($data['parameters']);
        }
        $config = Yaml::parse($this->configPath);
        if (is_array($config)) {
            $this->parseParam($config);
        }

        foreach ($this->modulesPath as $modulePath) {
            $serviceFile = $modulePath . '/' . Module::SERVICE;
            $data = Yaml::parse($serviceFile);
            $this->parseServices($data['services']);
            if (array_key_exists('tasks', $data)) {
                $this->parseTasks($data['tasks']);
            }
        }


        $this->createFile();
        $this->saveParams();
        $this->saveServices();
        $this->saveTasks();
    }

    /**
     * @param array $params
     */
    private function parseParam($params)
    {
        if(is_array($params)) {
            $this->parameters = array_merge($this->parameters, $params);
        }
    }

    private function parseTasks($tasks)
    {
        if(is_array($tasks)) {
            foreach ($tasks as $taskName => $taskParams) {
                $this->parseTasksParam($taskName, $taskParams);
            }
        }
    }

    private function parseTasksParam($taskName, $taskParams)
    {
        $task['class'] = $this->getClassValue($taskParams['class']);
        $task['action'] = $this->getClassValue($taskParams['action']);
        $task['description'] = '';
        if( array_key_exists('description', $taskParams) ) {
            $task['description'] =  $taskParams['description'];
        }
        $task['params'] = [];

        foreach ($taskParams['arguments'] as $argument) {
            $task['params'][] = $this->getArgumentsValue($argument);
        }
        $this->tasks[$taskName] = $task;
    }

    private function parseServices($services)
    {
        if(is_array($services)) {
            foreach ($services as $serviceName => $serviceParam) {
                $this->parseServiceParam($serviceName, $serviceParam);
            }
        }
    }

    private function parseServiceParam($serviceName, $serviceParam)
    {
        if (array_key_exists('event_name', $serviceParam)) {
            $parser = new Listener($this);
            $parser->setEventName($serviceParam['event_name']);
            $parser->setEventMethod($serviceParam['event_method']);
        } else if (array_key_exists('factory_service', $serviceParam)) {
            $parser = new Factory($this);
            $parser->setFactoryClass($serviceParam['factory_service']);
            $parser->setFactoryMethod($serviceParam['factory_method']);
        } else {
            $parser = new Standard($this);
        }

        $parser->setServiceName($serviceName);
        $parser->setClassName($this->getClassValue($serviceParam['class']));
        foreach ($serviceParam['arguments'] as $argument) {
            $parser->addArgument($this->getArgumentsValue($argument));
        }
        $this->servicesData[] = $parser;
    }


    /**
     * @param string $value
     * @return string
     */
    private function getClassValue($value)
    {
        $result = $this->parseParameter($value);
        if (!is_null($result)) {
            return $result;
        }

        return $value;
    }

    private function getArgumentsValue($value)
    {
        $result = $this->parseReferer($value);
        if (!is_null($result)) {
            return [
                'type' => 'service',
                'name' => $result
            ];
        }

        $v = $value;

        $result = $this->parseParameter($value);
        if (!is_null($result)) {
            $v = $result;
        }

        return [
            'type' => 'parameter',
            'value' => $v
        ];
    }



    /**
     * @param $text
     * @return null|string
     */
    private function parseParameter($text)
    {
        if (preg_match('/^%([a-zA-Z_0-9-\\\.]+)%$/',$text,$matches)) {
            return $this->parameters[$matches[1]];
        }

        return null;
    }

    /**
     * @param $text
     * @return null|string
     */
    private function parseReferer($text)
    {
        if (preg_match('/^@([a-zA-Z_0-9-\\\.]+)$/',$text,$matches)) {
            return $matches[1];
        }

        return null;
    }


    private function saveParams()
    {
        $this->writeLine("\t" . 'function _loadConfig($di)');
        $this->writeLine("\t{");
        $this->writeLine("\t\t" . '$config = new \mirolabs\phalcon\Framework\Map;');
        $this->writeLine("\t\t" . '$di->set(\'config\',$config);');
        foreach ($this->parameters as $key => $value) {

            $this->writeLine(sprintf("\t\t\$config->set('%s', '%s');", $key, json_encode($value)));
        }
        $this->writeLine("\t}\n");
    }


    private function saveServices()
    {
        $this->writeLine("\t" . 'function _loadServices($di)');
        $this->writeLine("\t{");
        foreach ($this->servicesData as $parser) {
            $parser->writeDefinition();
        }
        $this->writeLine("\t}\n");
    }

    private function saveTasks()
    {
        @file_put_contents($this->cacheDir . '/' . self::CACHE_TASKS, serialize($this->tasks));
        @chmod($this->cacheDir . '/' . self::CACHE_TASKS, 0777);
    }

    private function createFile()
    {
        @file_put_contents($this->cacheDir . '/' . self::CACHE_FILE, "<?php\n\n");
        @chmod($this->cacheDir . '/' . self::CACHE_FILE, 0777);
    }

    /**
     * @param string $line
     */
    public function writeLine($line)
    {
        file_put_contents(
            $this->cacheDir . '/' . self::CACHE_FILE,
            sprintf("%s\n", $line),
            FILE_APPEND
        );
    }

} 