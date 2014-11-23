<?php
namespace tests\mirolabs\phalcon\Framework\Container;

use mirolabs\phalcon\Framework\Container\Parser;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class ParserTest extends \UnitTestCase
{
    private $module1;
    private $module2;

    private $configFile;


    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        vfsStream::setup('root');
        vfsStream::newFile('modules/module1/config/services.yml')->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('modules/module2/config/services.yml')->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('config/config.yml')->at(vfsStreamWrapper::getRoot());

        $this->module1 = vfsStream::url('root/modules/module1');
        $this->module2 = vfsStream::url('root/modules/module2');
        $this->configFile = vfsStream::url('root/config/config.yml');

        parent::setUp($di, $config);
    }

    public function testLoadModule()
    {
        $this->createParameters();
        $this->createModule1();
        $this->createModule2();

        $parser = \Mockery::mock(
            'mirolabs\phalcon\Framework\Container\Parser',
            [[$this->module1, $this->module2], $this->configFile, 'cache']
        )
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $containerBuilder = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder');
        $taskBuilder = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder');
        $modelFactory = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\ModelFactory');
        $attributeParser = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $output = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\Output');
        $task = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\Model\Task');

        $task
            ->shouldReceive('getTaskValue')
            ->twice()
            ->andReturn(['task' => 'value']);

        $containerBuilder
            ->shouldReceive('writeLine');

        $taskBuilder
            ->shouldReceive('write');

        $output
            ->shouldReceive('writeDefinition');

        $parser
            ->shouldReceive('getDefinitionBuilder')
            ->with('cache/container.php')
            ->andReturn($containerBuilder);

        $parser
            ->shouldReceive('getDefinitionBuilder')
            ->with('cache/.task.log')
            ->andReturn($taskBuilder);

        $parser
            ->shouldReceive('getModelFactory')
            ->andReturn($modelFactory);

        $parser
            ->shouldReceive('getAttributeParser')
            ->andReturn($attributeParser);

        $parser
            ->shouldReceive('getTask')
            ->andReturn($task);

        $modelFactory
            ->shouldReceive('getServiceModel')
            ->andReturn($output);

        $parser->execute();
    }

    private function createParameters()
    {
        $this->addParametersToConfig([
            'param1' => 50,
            'param2' => 50
        ]);
    }

    private function createModule1()
    {
        $this->addParametersToModule($this->module1, ['service1' => 'class', 'param1' => 100]);
        $this->createServices($this->module1);
        $this->addService($this->module1, 'service.module1', '%service1%', ['%param1%']);
        $this->addListenerEvents(
            $this->module1,
            'listener.module1',
            'Listener',
            [
                'event.before' => 'before',
                'event.after' => 'after'
            ],
            []
        );
        $this->createTasks($this->module1);
        $this->addTask($this->module1, 'service.task', 'class', 'action', 'opis', []);
    }


    private function createModule2()
    {
        $this->addParametersToModule($this->module2, ['service2' => 'class', 'param2' => 100]);
        $this->createServices($this->module2);
        $this->addService($this->module2, 'service.module2', '%service2%', ['%param2%']);
        $this->createTasks($this->module2);
        $this->addTask($this->module2, 'service.task', 'class', 'action', 'opis', []);
    }


    private function addParametersToConfig(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            file_put_contents($this->configFile, sprintf("%s: %s\n", $key, $value), FILE_APPEND);
        }
    }

    private function addParametersToModule($module, array $parameters)
    {
        $path = $module . '/config/services.yml';

        file_put_contents($path, "parameters:\n", FILE_APPEND);
        foreach ($parameters as $key => $value) {
            file_put_contents($path, sprintf("  %s: %s\n", $key, $value), FILE_APPEND);
        }
    }

    private function createServices($module)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, "services:\n", FILE_APPEND);
    }

    private function createTasks($module)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, "tasks:\n", FILE_APPEND);
    }

    private function addService($module, $serviceName, $class, array $arguments)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, sprintf("  %s:\n", $serviceName), FILE_APPEND);
        file_put_contents($path, sprintf("    class: %s\n", $class), FILE_APPEND);
        file_put_contents($path, sprintf("    arguments:\n"), FILE_APPEND);
        foreach ($arguments as $argument) {
            file_put_contents($path, sprintf("      - %s\n", $argument), FILE_APPEND);
        }
    }

    private function addFactory($module, $serviceName, $class, $factoryService, $factoryMethod, array $arguments)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, sprintf("  %s:\n", $serviceName), FILE_APPEND);
        file_put_contents($path, sprintf("    class: %s\n", $class), FILE_APPEND);
        file_put_contents($path, sprintf("    factory_service: %s\n", $factoryService), FILE_APPEND);
        file_put_contents($path, sprintf("    factory_method: %s\n", $factoryMethod), FILE_APPEND);
        file_put_contents($path, sprintf("    arguments:\n"), FILE_APPEND);
        foreach ($arguments as $argument) {
            file_put_contents($path, sprintf("      - %s\n", $argument), FILE_APPEND);
        }
    }

    private function addListener($module, $serviceName, $class, $eventName, $eventMethod, array $arguments)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, sprintf("  %s:\n", $serviceName), FILE_APPEND);
        file_put_contents($path, sprintf("    class: %s\n", $class), FILE_APPEND);
        file_put_contents($path, sprintf("    event_name: %s\n", $eventName), FILE_APPEND);
        file_put_contents($path, sprintf("    event_method: %s\n", $eventMethod), FILE_APPEND);
        file_put_contents($path, sprintf("    arguments:\n"), FILE_APPEND);
        foreach ($arguments as $argument) {
            file_put_contents($path, sprintf("      - %s\n", $argument), FILE_APPEND);
        }
    }

    private function addListenerEvents($module, $serviceName, $class, array $events, array $arguments)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, sprintf("  %s:\n", $serviceName), FILE_APPEND);
        file_put_contents($path, sprintf("    class: %s\n", $class), FILE_APPEND);
        file_put_contents($path, sprintf("    events:\n"), FILE_APPEND);
        foreach ($events as $eventName => $eventMethod) {
            file_put_contents($path, sprintf("    - {event_name: %s, event_method: %s}\n", $eventName, $eventMethod), FILE_APPEND);
        }
        file_put_contents($path, sprintf("    arguments:\n"), FILE_APPEND);
        foreach ($arguments as $argument) {
            file_put_contents($path, sprintf("      - %s\n", $argument), FILE_APPEND);
        }
    }


    private function addTask($module, $serviceName, $class, $action, $description, $arguments)
    {
        $path = $module . '/config/services.yml';
        file_put_contents($path, sprintf("  %s:\n", $serviceName), FILE_APPEND);
        file_put_contents($path, sprintf("    class: %s\n", $class), FILE_APPEND);
        file_put_contents($path, sprintf("    action:\n", $action), FILE_APPEND);

        if (!is_null($description)) {
            file_put_contents($path, sprintf("    description:\n", $description), FILE_APPEND);
        }
        file_put_contents($path, sprintf("    arguments:\n"), FILE_APPEND);
        foreach ($arguments as $argument) {
            file_put_contents($path, sprintf("      - %s\n", $argument), FILE_APPEND);
        }
     }
}
 