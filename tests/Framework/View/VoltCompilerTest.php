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
     * @var MockInterface
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
        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/modules/test/views/front/hello.volt',
                'cache/vfs:%%%%root%%modules%%test%%views%%front%%hello.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }

    public function testOverwrittenPath()
    {
        $this->createFolders();
        $this->createFile('common/views/modules/test/front/hello.volt');
        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/common/views/modules/test/front/hello.volt',
                'cache/vfs:%%%%root%%common%%views%%modules%%test%%front%%hello.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }


    public function testCommonPath()
    {
        $this->createFolders();
        $this->createFile('common/views/modules/test/front/hello.volt');
        $this->createFile('common/views/front/hello.volt');
        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/common/views/front/hello.volt',
                'cache/vfs:%%%%root%%common%%views%%front%%hello.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }


    public function testCommonPathProd()
    {
        $this->createFolders();
        $this->createFile('common/views/modules/test/front/hello.volt');
        $this->createFile('common/views/front/hello.volt');
        $templateModulePath = $this->createFile('modules/test/views/front/hello.volt');
        $this->setOptions('test', 'prod');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/common/views/front/hello.volt',
                'cache/vfs:%%%%root%%common%%views%%front%%hello.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }

    public function testIndexDevPath()
    {
        $this->createFolders();
        $this->createFile('common/views/index.volt');
        $templateModulePath = $this->createFile('modules/test/views/index.volt');
        $this->setOptions('test');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/common/views/index.volt',
                'cache/vfs:%%%%root%%common%%views%%index.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }


    public function testIndexProdPath()
    {
        $this->createFolders();
        $this->createFile('common/views/index_deploy.volt');
        $templateModulePath = $this->createFile('modules/test/views/index.volt');
        $this->setOptions('test', 'prod');
        $this->setModuleViewsDir('vfs://root/modules/test/views');
        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->with(
                'vfs://root/common/views/index_deploy.volt',
                'cache/vfs:%%%%root%%common%%views%%index_deploy.volt.compile',
                false)
            ->once();

        $this->voltCompiler
            ->shouldReceive('parentCompileFile')
            ->withAnyArgs()
            ->never();

        $this->voltCompiler->compileFile($templateModulePath, '', false);
        $this->voltCompiler->mockery_verify();
    }


    private function setOptions($moduleName, $environment = 'dev', $commonView = 'vfs://root/common/views',
                                $compiledExtension = '.compile', $compiledSeparator = "%%", $compiledPath = 'cache/'
    ) {
        $this->setOption(VoltCompiler::OPTION_ENVIRONMENT, $environment);
        $this->setOption(VoltCompiler::OPTION_COMPILED_PATH, $compiledPath);
        $this->setOption(VoltCompiler::OPTION_COMPILED_SEPARATOR, $compiledSeparator);
        $this->setOption(VoltCompiler::OPTION_COMPILED_EXTENSION, $compiledExtension);
        $this->setOption(VoltCompiler::OPTION_COMMON_VIEW, $commonView);
        $this->setOption(VoltCompiler::OPTION_MODULE_NAME, $moduleName);
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
            'modules' => []
        ];
        vfsStream::create($structure);
    }

    private function createFile($filePath)
    {
        vfsStream::newFile($filePath)->at(vfsStreamWrapper::getRoot());
        return vfsStream::url('root/' . $filePath);
    }

}
 