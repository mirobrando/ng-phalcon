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

    private function setOptions($moduleName, $commonView = 'common/views', $compiledExtension = '.compile',
                                $compiledSeparator = "%%", $compiledPath = 'cache/', $environment = 'dev'
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

    private function createFile($filePath)
    {
        vfsStream::setup('root');
        vfsStream::newFile($filePath)->at(vfsStreamWrapper::getRoot());
        return vfsStream::url('root/' . $filePath);
    }

}
 