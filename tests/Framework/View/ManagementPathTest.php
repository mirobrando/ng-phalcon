<?php

namespace tests\mirolabs\phalcon\Framework\View;


use mirolabs\phalcon\Framework\Application;
use mirolabs\phalcon\Framework\View\ManagementPath;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class PathManagementTest extends \UnitTestCase
{

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        $this->createFolders();
        parent::setUp($di, $config);
    }

    public function testShouldBePathOfModule()
    {
        $modulePath = $this->createFile('modules/test/views/controller/action.volt');
        $path = $this->getManagementPath()->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'controller/action',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);
    }


    public function testShouldBePathWithOverwrittenModule()
    {
        $this->createFile('modules/test/views/controller/action.volt');
        $modulePath = $this->createFile('common/views/modules/test/controller/action.volt');
        $path = $this->getManagementPath()->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'controller/action',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);

    }


    public function testShouldBePathWithCommonPath()
    {
        $modulePath = $this->createFile('common/views/controller/action.volt');
        $this->createFile('modules/test/views/controller/action.volt');
        $this->createFile('common/views/modules/test/controller/action.volt');
        $path = $this->getManagementPath()->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'controller/action',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);

    }

    public function testShouldBePathWithCommonPathWhenEnvironmentIsProd()
    {
        $modulePath = $this->createFile('common/views/controller/action.volt');
        $this->createFile('modules/test/views/controller/action.volt');
        $this->createFile('common/views/modules/test/controller/action.volt');
        $path = $this->getManagementPath(Application::ENVIRONMENT_PROD)->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'controller/action',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);

    }

    public function testShouldBeIndex()
    {
        $modulePath = $this->createFile('common/views/index.volt');
        $this->createFile('modules/test/views/index.volt');
        $this->createFile('common/views/modules/test/index.volt');
        $path = $this->getManagementPath()->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'index',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);

    }

    public function testShouldBeIndexDeploy()
    {
        $this->createFile('common/views/index.volt');
        $modulePath = $this->createFile('common/views/index_deploy.volt');
        $this->createFile('modules/test/views/index.volt');
        $this->createFile('common/views/modules/test/index.volt');
        $path = $this->getManagementPath(Application::ENVIRONMENT_PROD)->getTemplatePath(
            'test',
            'vfs://root/modules/test/views/',
            'index',
            '.volt'
        );
        $this->assertEquals($modulePath, $path);
    }



    private function getManagementPath($environment= Application::ENVIRONMENT_DEV)
    {
        return new ManagementPath('vfs://root/common/views/', $environment);
    }



    private function createFolders()
    {
        vfsStream::setup('root');
        $structure = [
            'common' => [
                'views' => ['modules' => []]
            ],
            'modules' => [],
            'cache'
        ];
        vfsStream::create($structure);
    }

    private function createFile($filePath, $body = 'hello')
    {
        vfsStream::newFile($filePath)->at(vfsStreamWrapper::getRoot());
        $path = vfsStream::url('root/' . $filePath);
        file_put_contents($path, $body);
        return $path;
    }
}