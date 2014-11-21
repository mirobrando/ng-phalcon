<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Model\Factory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class FactoryTest extends \UnitTestCase
{

    private $file;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        vfsStream::setup('root');
        vfsStream::newFile('file.php')->at(vfsStreamWrapper::getRoot());
        $this->file = vfsStream::url('root/file.php');

        parent::setUp($di, $config);
    }

    public function testWriteFactory()
    {
        $fileBuilderMock = \Mockery::mock('mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'factory_service' => 'test.factoryService',
            'factory_method' => 'getService',
            'class' => 'test\Class',
            'arguments' => ['attribute1', 'attribute2']
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->never();

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->times(2)
            ->andReturn(
                ['type' => 'service', 'name' => 'service.reference'],
                ['type' => 'parameter', 'value' => 'value']
            );

        $factory = new Factory($attributeParserMock, 'test.factory', $value);
        $factory->writeDefinition(new DefinitionBuilder($this->file, $fileBuilderMock));

        $expectedResult =
            "\t\t\$di->set('test.factory', function() use (\$di) {\n" .
            "\t\t\treturn \$di->get('test.factoryService')->getService(\$di->get('service.reference'), 'value');\n" .
            "\t\t});\n";

        $this->assertEquals($expectedResult, file_get_contents($this->file));
    }
}
 