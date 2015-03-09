<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\AnnotationParser;
use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Model\Service;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class ServiceTest extends \UnitTestCase
{

    private $file;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        vfsStream::setup('root');
        vfsStream::newFile('file.php')->at(vfsStreamWrapper::getRoot());
        $this->file = vfsStream::url('root/file.php');

        parent::setUp($di, $config);
    }

    public function testWriteService()
    {
        $fileBuilderMock = \Mockery::mock('mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'class' => 'test\Class',
            'arguments' => ['attribute1', 'attribute2']
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->twice()
            ->andReturn('test\Class');

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->times(2)
            ->andReturn(
                ['type' => 'service', 'name' => 'service.reference'],
                ['type' => 'parameter', 'value' => 'value']
            );

        $service = new Service(
            $attributeParserMock,
            new AnnotationParser([], $this->getAnnotationMock()),
            'test.service',
            $value
        );
        $service->writeDefinition(new DefinitionBuilder($this->file, $fileBuilderMock));


        $expectedResult =
            "\t\t\$di->set('test.service', [\n" .
            "\t\t\t'className' => 'test\\Class',\n" .
            "\t\t\t'arguments' => [\n" .
            "\t\t\t\t['type' => 'service', 'name' => 'service.reference'],\n" .
            "\t\t\t\t['type' => 'parameter', 'value' => 'value']\n" .
            "\t\t\t],\n" .
            "\t\t\t'properties' => [\n\n" .
            "\t\t\t]\n" .
            "\t\t], true);\n";

        $this->assertEquals($expectedResult, file_get_contents($this->file));
        $fileBuilderMock->mockery_verify();
        $attributeParserMock->mockery_verify();


    }

    private function getAnnotationMock()
    {
        $annotationMock = \Mockery::mock('Phalcon\Annotations\Adapter');
        $annotationMock
            ->shouldReceive('getProperties')
            ->andReturn([]);

        return $annotationMock;
    }
}
 