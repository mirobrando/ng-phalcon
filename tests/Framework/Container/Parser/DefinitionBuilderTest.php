<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class DefinitionBuilderTest extends  \UnitTestCase
{

    private $file;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        vfsStream::setup('root');
        vfsStream::newFile('file.php')->at(vfsStreamWrapper::getRoot());
        $this->file = vfsStream::url('root/file.php');

        parent::setUp($di, $config);
    }




    public function testCreateFile()
    {
        $mock = \Mockery::mock('\mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $mock
            ->shouldReceive('createFile')
            ->once()
            ->with($this->file, 0777, true);

        $definitionBuilder = new DefinitionBuilder($this->file, $mock);
        $definitionBuilder->createFile();
    }

    public function testWrite()
    {
        $mock = \Mockery::mock('\mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $definitionBuilder = new DefinitionBuilder($this->file, $mock);
        $definitionBuilder->write('test message');

        $this->assertEquals('test message', file_get_contents($this->file));
    }

    public function testWriteLine()
    {
        $mock = \Mockery::mock('\mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $definitionBuilder = new DefinitionBuilder($this->file, $mock);
        $definitionBuilder->writeLine('test message');

        $this->assertEquals("test message\n", file_get_contents($this->file));
    }
}
 