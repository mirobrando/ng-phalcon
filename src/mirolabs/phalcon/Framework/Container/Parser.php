<?php

namespace mirolabs\phalcon\Framework\Container;


use mirolabs\phalcon\Framework\Container\Parser\Factory;
use mirolabs\phalcon\Framework\Container\Parser\Standard;
use Symfony\Component\Yaml\Yaml;

class Parser implements Output
{
    const CACHE_FILE = 'container.php';

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
            $serviceFile = $modulePath . '/' . Module::CONFIG;
            $data = Yaml::parse($serviceFile);
            $this->parseParam($data['parameters']);
            $this->parseServices($data['services']);
        }

        $config = Yaml::parse($this->configPath);
        if (is_array($config)) {
            $this->parseParam($config);
        }
        $this->createFile();
        $this->saveParams();

    }

    /**
     * @param array $params
     */
    private function parseParam($params)
    {
        $this->parameters = array_merge($this->parameters, $params);
    }

    private function parseServices($services)
    {
        foreach ($services as $serviceName => $serviceParam) {
            $this->parseServiceParam($serviceName, $serviceParam);
        }
    }

    private function parseServiceParam($serviceName, $serviceParam)
    {
        if (array_key_exists('factory_service', $serviceParam)) {
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
            $result;
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
            'value' => $value
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
            $this->writeLine(sprintf("\t\t\$config->set('%s', '%s');", $key,$value));
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

    private function createFile()
    {
        file_put_contents($this->cacheDir . '/' . self::CACHE_FILE, "<?php\n\n");
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