<?php

namespace tests\mirolabs\phalcon\Framework\View;


use mirolabs\phalcon\Framework\View\Volt;
use Phalcon\DI;
use Phalcon\Mvc\View;

class VoltTest extends \UnitTestCase
{
    public function testCompilerObject()
    {
        $dependencyInjection = new DI();
        $volt = new Volt(new View(), $dependencyInjection);
        $volt->setOptions(['param' => 'value']);
        $compiler = $volt->getCompiler();
        $this->assertInstanceOf('mirolabs\phalcon\Framework\View\VoltCompiler', $compiler);
        $this->assertEquals('value', $compiler->getOption('param'));
        $this->assertEquals($dependencyInjection, $compiler->getDI());
    }
}
 