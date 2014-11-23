<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Model\Listener;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Phalcon\Config;
use Phalcon\DiInterface;

class ListenerTest extends \UnitTestCase
{
    private $file;

    public function setUp(DiInterface $di = NULL, Config $config = NULL)
    {
        vfsStream::setup('root');
        vfsStream::newFile('file.php')->at(vfsStreamWrapper::getRoot());
        $this->file = vfsStream::url('root/file.php');

        parent::setUp($di, $config);
    }


    public function testWriteSimpleEvents()
    {
        $fileBuilderMock = \Mockery::mock('mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'event_name' => 'test.event',
            'event_method' => 'subscribe',
            'class' => 'test\Class'
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->andReturn('test\Class');

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->never();

        $factory = new Listener($attributeParserMock, 'test.listener', $value);
        $factory->writeDefinition(new DefinitionBuilder($this->file, $fileBuilderMock));

        $expectedResult =
            "\t\t\$di->set('test.listener', [\n" .
            "\t\t\t'className' => 'test\\Class',\n" .
            "\t\t\t'arguments' => [\n\n" .
            "\t\t\t]\n" .
            "\t\t]);\n" .
            "\t\t\$di->get('listener')->attach('test.event', function(\$event, \$component) use (\$di) {\n" .
            "\t\t\t\$di->get('test.listener')->subscribe(\$event, \$component);\n" .
            "\t\t});\n";

        $this->assertEquals($expectedResult, file_get_contents($this->file));
        $fileBuilderMock->mockery_verify();
        $attributeParserMock->mockery_verify();

    }

    public function testWriteMultiEvents()
    {
        $fileBuilderMock = \Mockery::mock('mirolabs\phalcon\Framework\Tasks\FileBuilder');
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'events' => [
                [
                    'event_name' => 'test.event',
                    'event_method' => 'subscribe'
                ],
                [
                    'event_name' => 'test.secondEvent',
                    'event_method' => 'subscribe'
                ]
            ],
            'class' => 'test\Class'
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->andReturn('test\Class');

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->never();

        $factory = new Listener($attributeParserMock, 'test.listener', $value);
        $factory->writeDefinition(new DefinitionBuilder($this->file, $fileBuilderMock));

        $expectedResult =
            "\t\t\$di->set('test.listener', [\n" .
            "\t\t\t'className' => 'test\\Class',\n" .
            "\t\t\t'arguments' => [\n\n" .
            "\t\t\t]\n" .
            "\t\t]);\n" .
            "\t\t\$di->get('listener')->attach('test.event', function(\$event, \$component) use (\$di) {\n" .
            "\t\t\t\$di->get('test.listener')->subscribe(\$event, \$component);\n" .
            "\t\t});\n" .
            "\t\t\$di->get('listener')->attach('test.secondEvent', function(\$event, \$component) use (\$di) {\n" .
            "\t\t\t\$di->get('test.listener')->subscribe(\$event, \$component);\n" .
            "\t\t});\n";

        $this->assertEquals($expectedResult, file_get_contents($this->file));
        $fileBuilderMock->mockery_verify();
        $attributeParserMock->mockery_verify();

    }

}
 