<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\Model\Parameter;

class ParameterTest extends \UnitTestCase
{
    public function testParameter()
    {
        $value = new \stdClass();
        $value->name = 'test';
        $value->number = 123;

        $configLine = "\t\t\$config->set('test.param.key', '{\"name\":\"test\",\"number\":123}');";

        $mock = \Mockery::mock('mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder');
        $mock
            ->shouldReceive('writeLine')
            ->once()
            ->with($configLine);

        $parameter = new Parameter('test.param.key', $value);
        $parameter->writeDefinition($mock);
        $this->assertEquals($value, $parameter->getValue());
    }
}

