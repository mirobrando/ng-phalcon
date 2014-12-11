<?php

namespace tests\mirolabs\phalcon\Framework\View;

use mirolabs\phalcon\Framework\View\VoltCompiler;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class VoltCompilerTest extends \UnitTestCase
{
    /**
     * @var VoltCompiler
     */
    private $voltCompiler;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        $this->voltCompiler = \Mockery::mock('mirolabs\phalcon\Framework\View\VoltCompiler')
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        parent::setUp($di, $config);
    }

    public function testModulePath()
    {
        $this->createFolders();
        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        file_put_contents($templateModulePath, 'hello');
        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');

        $this->assertEquals('hello', $this->voltCompiler->compile($templateModulePath, false));

        $this->voltCompiler->mockery_verify();
    }

    public function testOverwrittenPath()
    {
        $this->createFolders();
        $templateModulePath = $this->createFile('common/views/modules/test/front/hello.volt');
        file_put_contents($templateModulePath, 'overwritten');

        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        file_put_contents($templateModulePath, 'hello');

        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->assertEquals('overwritten', $this->voltCompiler->compile($templateModulePath, false));
        $this->voltCompiler->mockery_verify();
    }


    public function testCommonPath()
    {
        $this->createFolders();
        $templateModulePath = $this->createFile('common/views/modules/test/front/hello.volt');
        file_put_contents($templateModulePath, 'overwritten');

        $templateModulePath = $this->createFile('common/views/front/hello.volt');
        file_put_contents($templateModulePath, 'common');

        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        file_put_contents($templateModulePath, 'hello');

        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->assertEquals('common', $this->voltCompiler->compile($templateModulePath, false));
        $this->voltCompiler->mockery_verify();
    }


    public function testCommonPathProd()
    {
        $this->createFolders();
        $templateModulePath = $this->createFile('common/views/modules/test/front/hello.volt');
        file_put_contents($templateModulePath, 'overwritten');

        $templateModulePath = $this->createFile('common/views/front/hello.volt');
        file_put_contents($templateModulePath, 'common');

        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        file_put_contents($templateModulePath, 'hello');

        $this->setOptions('test', 'prod');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->assertEquals('common', $this->voltCompiler->compile($templateModulePath, false));
        $this->voltCompiler->mockery_verify();
    }

    public function testIndexDevPath()
    {
        $this->createFolders();
        $templateModulePath =$this->createFile('common/views/index.volt');
        file_put_contents($templateModulePath, 'common');

        $templateModulePath = $this->createFile('modules/test/views/index.volt');
        file_put_contents($templateModulePath, 'hello');

        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->assertEquals('common', $this->voltCompiler->compile($templateModulePath, false));
        $this->voltCompiler->mockery_verify();
    }


    public function testIndexProdPath()
    {
        $this->createFolders();
        $templateModulePath = $this->createFile('common/views/index_deploy.volt');
        file_put_contents($templateModulePath, 'prod');

        $templateModulePath = $this->createFile('modules/test/views/index.volt');
        file_put_contents($templateModulePath, 'dev');

        $this->setOptions('test', 'prod');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->assertEquals('prod', $this->voltCompiler->compile($templateModulePath, false));
        $this->voltCompiler->mockery_verify();
    }


    private function setOptions($moduleName, $environment = 'dev', $commonView = 'vfs://root/common/views',
                                $compiledExtension = '.compile', $compiledSeparator = "%%",
                                $compiledPath = 'vfs://root/cache'
    ) {
        $this->voltCompiler->setOption(VoltCompiler::OPTION_ENVIRONMENT, $environment);
        $this->voltCompiler->setOption(VoltCompiler::OPTION_COMPILED_PATH, $compiledPath);
        $this->voltCompiler->setOption(VoltCompiler::OPTION_COMPILED_SEPARATOR, $compiledSeparator);
        $this->voltCompiler->setOption(VoltCompiler::OPTION_COMPILED_EXTENSION, $compiledExtension);
        $this->voltCompiler->setOption(VoltCompiler::OPTION_COMMON_VIEW, $commonView);
        $this->voltCompiler->setOption(VoltCompiler::OPTION_MODULE_NAME, $moduleName);
    }


    private function setOption($option, $value)
    {
        $this->voltCompiler
            ->shouldReceive('getOption')
            ->with($option)
            ->andReturn($value);
    }

    private function setModuleViewsDir($moduleDir)
    {
        $this->voltCompiler
            ->shouldReceive('getModuleViewsDir')
            ->andReturn($moduleDir);
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

    private function createFile($filePath)
    {
        vfsStream::newFile($filePath)->at(vfsStreamWrapper::getRoot());
        return vfsStream::url('root/' . $filePath);
    }

}
 