<?php

namespace tests\mirolabs\phalcon\Framework\Container;

use mirolabs\phalcon\Framework\Application;
use mirolabs\phalcon\Framework\Container\Check;
use mirolabs\phalcon\Framework\Module;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class CheckTest extends \UnitTestCase
{

    private $configuration;
    private $servicesDemo;
    private $servicesTest;
    private $projectPath;
    private $modules;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        $this->createFolders();
        $this->configuration = $this->createFile('config', 'config.yml');
        $this->servicesDemo = $this->createFile('modules/demo/config', 'services.yml');
        $this->servicesTest = $this->createFile('modules/test/config', 'services.yml');
        $this->projectPath = 'vfs://root/';
        $this->modules = [
            'test' => 'vfs://root/modules/test/',
            'demo' => 'vfs://root/modules/demo/',
            'fail'=> 'vfs://root/modules/fail/'
        ];
        parent::setUp($di, $config);
    }


    public function testShouldBeTrueWhenCallFirstTime()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $this->assertTrue($check->isChangeConfiguration());
    }


    public function testShouldBeFalseWhenCallSecondTime()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->assertFalse($check->isChangeConfiguration());
    }


    public function testShouldBeTrueWhenChangedConfig()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->updateFile($this->configuration);
        $this->assertTrue($check->isChangeConfiguration());
    }

    public function testShouldBeFalseWhenChangedConfigAndEnvironmentIsProduction()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_PROD);
        $check->isChangeConfiguration();
        $this->updateFile($this->configuration);
        $this->assertFalse($check->isChangeConfiguration());
    }

    public function testShouldBeTrueWhenChangedServiceConfig()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->updateFile($this->servicesTest);
        $this->assertTrue($check->isChangeConfiguration());
    }

    public function testShouldBeTrueWhenNewService()
    {
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->createFile('modules/test/services', 'TestService.php');
        $this->assertTrue($check->isChangeConfiguration());
    }

    public function testShouldBeTrueWhenChangedService()
    {
        $this->createFile('modules/test/services', 'TestService.php');
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->updateFile('vfs://root/modules/test/services/TestService.php');
        $this->assertTrue($check->isChangeConfiguration());
    }

    public function testShouldBeFalseWhenWithoutChange()
    {
        $this->createFile('modules/test/services', 'TestService.php');
        $check = new Check($this->projectPath, $this->modules, Application::ENVIRONMENT_DEV);
        $check->isChangeConfiguration();
        $this->assertFalse($check->isChangeConfiguration());
    }

    private function updateFile($filePath)
    {
        $data = file_get_contents($this->projectPath . Module::COMMON_CACHE . '/' . Check::CACHE_FILE);
        $tasks = unserialize($data);
        $tasks[$filePath]--;
        file_put_contents($this->projectPath . Module::COMMON_CACHE . '/' . Check::CACHE_FILE, serialize($tasks));
    }

    private function createFile($folder, $filePath)
    {
        vfsStream::newFile($filePath)->at(vfsStreamWrapper::getRoot()->getChild($folder));

        $path = vfsStream::url('root/' . $folder . '/' . $filePath);
        file_put_contents($path, 'test');
        return $path;
    }

    private function createFolders()
    {
        vfsStream::setup('root');
        $structure = [
            'common' => [
                'views' => ['modules' => []],
                'cache' => []
            ],
            'config' => [],
            'modules' => [
                'demo' => [
                    'config' => [],
                    'controllers' => [],
                    'services' => []
                ],
                'test' => [
                    'config' => [],
                    'controllers' => [],
                    'services' => []
                ],
                'fail' => []
            ],
        ];
        vfsStream::create($structure);
    }


}
