<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\Model\Task;

class TaskTest extends \UnitTestCase
{
    public function testGetTaskWithDescription()
    {
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'class' => 'test\Task',
            'action' => 'callTask',
            'description' => 'this is test task',
            'arguments' => ['attribute1', 'attribute2']
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->with('test\Task')
            ->andReturn('test\Task');

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->with('callTask')
            ->andReturn('callTask');

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->once()
            ->with('attribute1')
            ->andReturn(['type' => 'service', 'name' => 'service.reference']);

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->once()
            ->with('attribute2')
            ->andReturn(['type' => 'parameter', 'value' => 'value']);


        $task = new Task($attributeParserMock, 'task.test', $value);
        $expectedResult = [
            'class' => 'test\Task',
            'action' => 'callTask',
            'description' => 'this is test task',
            'params' => [
                ['type' => 'service', 'name' => 'service.reference'],
                ['type' => 'parameter', 'value' => 'value']
            ]
        ];
        $this->assertEquals($expectedResult, $task->getTaskValue());
        $attributeParserMock->mockery_verify();
    }


    public function testGetTaskWithoutDescription()
    {
        $attributeParserMock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\AttributeParser');
        $value = [
            'class' => 'test\Task',
            'action' => 'callTask',
            'arguments' => ['attribute1', 'attribute2']
        ];

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->with('test\Task')
            ->andReturn('test\Task');

        $attributeParserMock
            ->shouldReceive('getClassValue')
            ->once()
            ->with('callTask')
            ->andReturn('callTask');

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->once()
            ->with('attribute1')
            ->andReturn(['type' => 'service', 'name' => 'service.reference']);

        $attributeParserMock
            ->shouldReceive('getArgumentValue')
            ->once()
            ->with('attribute2')
            ->andReturn(['type' => 'parameter', 'value' => 'value']);


        $task = new Task($attributeParserMock, 'task.test', $value);
        $expectedResult = [
            'class' => 'test\Task',
            'action' => 'callTask',
            'description' => '',
            'params' => [
                ['type' => 'service', 'name' => 'service.reference'],
                ['type' => 'parameter', 'value' => 'value']
            ]
        ];
        $this->assertEquals($expectedResult, $task->getTaskValue());
        $attributeParserMock->mockery_verify();
    }

}
 