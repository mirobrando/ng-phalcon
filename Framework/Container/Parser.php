<?php

namespace mirolabs\phalcon\Framework\Container;

use mirolabs\phalcon\Framework\Container\Parser\AnnotationParser;
use mirolabs\phalcon\Framework\Container\Parser\AttributeParser;
use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Model\Parameter;
use mirolabs\phalcon\Framework\Container\Parser\Model\Task;
use mirolabs\phalcon\Framework\Container\Parser\ModelFactory;
use mirolabs\phalcon\Framework\Module;
use mirolabs\phalcon\Framework\Tasks\FileBuilder;
use Phalcon\Annotations\Adapter;
use Symfony\Component\Yaml\Yaml;

class Parser
{
    const CACHE_CONTAINER = 'container.php';
    const CACHE_TASKS = '.task.log';

    const ATTRIBUTE_SERVICE_PARAMETERS = 'parameters';
    const ATTRIBUTE_SERVICE_SERVICES = 'services';
    const ATTRIBUTE_SERVICE_TASKS = 'tasks';

    /**
     * @var Adapter
     */
    private $annotationAdapter;

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
     * @var AttributeParser
     */
    private $attributeParser;

    /**
     * @var AnnotationParser
     */
    private $annotationParser;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * @param string $modulesPath
     * @param string $configPath
     * @param string $cacheDir
     * @param Adapter $annotationAdapter
     */
    public function __construct($modulesPath, $configPath, $cacheDir, $annotationAdapter)
    {
        $this->modulesPath = $modulesPath;
        $this->configPath = $configPath;
        $this->cacheDir = $cacheDir;
        $this->annotationAdapter = $annotationAdapter;
    }

    /**
     * @codeCoverageIgnore
     * @param $file
     * @return DefinitionBuilder
     */
    protected function getDefinitionBuilder($file)
    {
        return new DefinitionBuilder($file, new FileBuilder($this->cacheDir));
    }

    /**
     * @codeCoverageIgnore
     * @return ModelFactory
     */
    protected function getModelFactory()
    {
        if (is_null($this->modelFactory)) {
            $this->modelFactory = new ModelFactory();
        }

        return $this->modelFactory;
    }

    /**
     * @codeCoverageIgnore
     * @return AttributeParser
     */
    protected function getAttributeParser()
    {
        if (is_null($this->attributeParser)) {
            $this->attributeParser = new AttributeParser($this->parameters);
        }

        return $this->attributeParser;
    }

    /**
     * @codeCoverageIgnore
     * @return AttributeParser
     */
    protected function getAnnotationParser()
    {
        if (is_null($this->annotationParser)) {
            $this->annotationParser = new AnnotationParser($this->parameters, $this->annotationAdapter);
        }

        return $this->annotationParser;
    }

    /**
     * @codeCoverageIgnore
     * @param $taskName
     * @param $taskParams
     * @return Task
     */
    protected function getTask($taskName, $taskParams)
    {
        return new Task($this->getAttributeParser(), $this->getAnnotationParser(), $taskName, $taskParams);
    }

    /**
     *
     */
    public function execute()
    {
        $this->createParameters();
        $this->createServices();
        $this->saveServices();
        $this->saveTasks();
    }


    /**
     *
     */
    private function createParameters()
    {
        foreach ($this->modulesPath as $modulePath) {
            $data = Yaml::parse(file_get_contents($modulePath . '/' . Module::SERVICE));
            $this->parseParameters($data[self::ATTRIBUTE_SERVICE_PARAMETERS]);
        }

        $config = Yaml::parse(file_get_contents($this->configPath));
        if (is_array($config)) {
            $this->parseParameters($config);
        }
    }

    /**
     * @param array $parameters
     */
    private function parseParameters($parameters)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $name => $value) {
                $this->parameters[$name] = new Parameter($name, $value);
            }
        }
    }

    /**
     *
     */
    private function createServices()
    {
        foreach ($this->modulesPath as $modulePath) {
            $data = Yaml::parse(file_get_contents($modulePath . '/' . Module::SERVICE));
            $this->parseServices($data[self::ATTRIBUTE_SERVICE_SERVICES]);
            if (array_key_exists(self::ATTRIBUTE_SERVICE_TASKS, $data)) {
                $this->parseTasks($data[self::ATTRIBUTE_SERVICE_TASKS]);
            }
        }
    }

    /**
     * @param $services
     */
    private function parseServices($services)
    {
        $modelFactory = $this->getModelFactory();
        if (is_array($services)) {
            foreach ($services as $serviceName => $serviceParameters) {
                $this->servicesData[$serviceName] = $modelFactory->getServiceModel(
                    $serviceName,
                    $serviceParameters,
                    $this->getAttributeParser(),
                    $this->getAnnotationParser()
                );
            }
        }
    }

    /**
     * @param array $tasks
     */
    private function parseTasks($tasks)
    {
        if (is_array($tasks)) {
            foreach ($tasks as $taskName => $taskParams) {
                $this->tasks[] = $this->getTask($taskName, $taskParams)->getTaskValue();
            }
        }
    }

    /**
     *
     */
    private function saveServices()
    {
        $definitionBuilder = $this->getDefinitionBuilder($this->cacheDir . '/' . self::CACHE_CONTAINER);
        $definitionBuilder->createFile();
        $definitionBuilder->writeLine("<?php\n\n");
        $definitionBuilder->writeLine("\t" . 'function _loadConfig($di)');
        $definitionBuilder->writeLine("\t{");
        $definitionBuilder->writeLine("\t\t" . '$config = new \mirolabs\phalcon\Framework\Map;');
        $definitionBuilder->writeLine("\t\t" . '$di->set(\'config\',$config);');
        foreach ($this->parameters as $parameter) {
            $parameter->writeDefinition($definitionBuilder);
        }
        $definitionBuilder->writeLine("\t}\n");
        $definitionBuilder->writeLine("\t" . 'function _loadServices($di)');
        $definitionBuilder->writeLine("\t{");
        foreach ($this->servicesData as $service) {
            $service->writeDefinition($definitionBuilder);
        }
        $definitionBuilder->writeLine("\t}\n");
    }

    /**
     *
     */
    private function saveTasks()
    {
        $definitionBuilder = $this->getDefinitionBuilder($this->cacheDir . '/' . self::CACHE_TASKS);
        $definitionBuilder->write(serialize($this->tasks));
    }
}
