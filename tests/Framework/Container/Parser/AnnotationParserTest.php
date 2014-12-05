<?php

namespace tests\mirolabs\phalcon\Framework\Container\Parser;



use mirolabs\phalcon\Framework\Container\Parser\AnnotationParser;
use mirolabs\phalcon\Framework\Container\Parser\Model\Parameter;

class AnnotationParserTest extends \UnitTestCase
{
    public function testService()
    {
        $annotation = \Mockery::mock('Phalcon\Annotations\Annotation');
        $annotation
            ->shouldReceive('getArgument')
            ->once()
            ->with(0)
            ->andReturn('service.test');


        $property = \Mockery::mock('Phalcon\Annotations\Collection');
        $property
            ->shouldReceive('has')
            ->once()
            ->with('Service')
            ->andReturn(true);
        $property
            ->shouldReceive('has')
            ->once()
            ->with('Value')
            ->andReturn(false);
        $property
            ->shouldReceive('get')
            ->once()
            ->with('Service')
            ->andReturn($annotation);
        $property
            ->shouldReceive('get')
            ->with('Value')
            ->never();


        $annotationParser = new AnnotationParser([], $this->getAnnotationMock('test', $property));
        $result = $annotationParser->getProperties('TestClass');
        $this->assertEquals(
            [
                [
                    'name' => 'test',
                    'value' =>['type' => 'service', 'name' => 'service.test']
                ]
            ],
            $result
        );

        $property->mockery_verify();
        $annotation->mockery_verify();
    }

    public function testValue()
    {
        $annotation = \Mockery::mock('Phalcon\Annotations\Annotation');
        $annotation
            ->shouldReceive('getArgument')
            ->once()
            ->with(0)
            ->andReturn('param.test');


        $property = \Mockery::mock('Phalcon\Annotations\Collection');
        $property
            ->shouldReceive('has')
            ->once()
            ->with('Service')
            ->andReturn(false);
        $property
            ->shouldReceive('has')
            ->once()
            ->with('Value')
            ->andReturn(true);
        $property
            ->shouldReceive('get')
            ->once()
            ->with('Value')
            ->andReturn($annotation);
        $property
            ->shouldReceive('get')
            ->with('Service')
            ->never();

        $parameter = new Parameter('param.test', 100);
        $annotationParser = new AnnotationParser(['param.test' => $parameter], $this->getAnnotationMock('test', $property));
        $result = $annotationParser->getProperties('TestClass');
        $this->assertEquals(
            [
                [
                    'name' => 'test',
                    'value' =>['type' => 'parameter', 'value' => 100]
                ]
            ],
            $result
        );

        $property->mockery_verify();
        $annotation->mockery_verify();
    }

    private function getAnnotationMock($name, $property)
    {
        $annotationMock = \Mockery::mock('Phalcon\Annotations\Adapter');
        $annotationMock
            ->shouldReceive('getProperties')
            ->andReturn([$name => $property]);

        return $annotationMock;
    }


}
 