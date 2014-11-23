<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser;


use mirolabs\phalcon\Framework\Container\Parser\AttributeParser;
use mirolabs\phalcon\Framework\Container\Parser\ModelFactory;

class ModelFactoryTest extends \UnitTestCase
{

    public function testListener()
    {
        $modelFactory = new ModelFactory();
        $result = $modelFactory->getServiceModel(
            'name',
            ['event_name' => 'event', 'event_method' => 'method'],
            new AttributeParser([])
        );

        $this->assertInstanceOf('mirolabs\phalcon\Framework\Container\Parser\Model\Listener', $result);
    }

    public function testListenerMultiEvents()
    {
        $modelFactory = new ModelFactory();
        $result = $modelFactory->getServiceModel(
            'name',
            ['events' => []],
            new AttributeParser([])
        );

        $this->assertInstanceOf('mirolabs\phalcon\Framework\Container\Parser\Model\Listener', $result);
    }

    public function testFactory()
    {
        $modelFactory = new ModelFactory();
        $result = $modelFactory->getServiceModel(
            'name',
            ['factory_service' => 'service', 'factory_method' => ['method']],
            new AttributeParser([])
        );

        $this->assertInstanceOf('mirolabs\phalcon\Framework\Container\Parser\Model\Factory', $result);
    }

    public function testService()
    {
        $modelFactory = new ModelFactory();
        $result = $modelFactory->getServiceModel(
            'name',
            [],
            new AttributeParser([])
        );

        $this->assertInstanceOf('mirolabs\phalcon\Framework\Container\Parser\Model\Service', $result);
    }

}
 