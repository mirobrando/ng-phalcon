<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser;

use mirolabs\phalcon\Framework\Container\Parser\AttributeParser;
use mirolabs\phalcon\Framework\Container\Parser\Model\Parameter;
use Phalcon\Exception;

class AttributeParserTest extends \UnitTestCase
{

    public function testParameterIsNotExists()
    {
        try {
            $attributeParser = new AttributeParser([]);
            $attributeParser->getClassValue('%test.key%');
            $this->fail('excepted exception when parameter is not exists');
        } catch (Exception $e) {
            $this->assertEquals('Parameter test.key is not exists', $e->getMessage());
        }
    }

    public function testClass()
    {
        $attributeParser = new AttributeParser([]);
        $this->assertEquals("test\\Class", $attributeParser->getClassValue("test\\Class"));
    }

    public function testClassWithParameter()
    {
        $parameters = ['test.class' => new Parameter('test.class',"test\\Class")];
        $attributeParser = new AttributeParser($parameters);
        $this->assertEquals("test\\Class", $attributeParser->getClassValue('%test.class%'));
    }

    public function testArgument()
    {
        $attributeParser = new AttributeParser([]);
        $this->assertEquals(
            ['type' => 'parameter', 'value' => 'value'],
            $attributeParser->getArgumentValue('value')
        );
    }

    public function testArgumentWithParameter()
    {
        $parameters = ['test.value' => new Parameter('test.value',"value")];
        $attributeParser = new AttributeParser($parameters);
        $this->assertEquals(
            ['type' => 'parameter', 'value' => 'value'],
            $attributeParser->getArgumentValue('%test.value%')
        );
    }

    public function testArgumentWithService()
    {
        $attributeParser = new AttributeParser([]);
        $this->assertEquals(
            ['type' => 'service', 'name' => 'test.service'],
            $attributeParser->getArgumentValue('@test.service')
        );
    }

}
 