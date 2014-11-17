<?php


class ClassBuilderTest extends  \UnitTestCase
{

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getClassBuilderMock()
    {
        return $this
            ->getMockBuilder('mirolabs\phalcon\Framework\Tasks\ClassBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['writeLine'])
            ->getMock();

    }

    public function testCreatePhpFile()
    {
        $mock = $this->getClassBuilderMock();
        $mock
            ->expects($this->once())
            ->method('writeLine')
            ->with("<?php\n");
        $mock->createPhpFile();
    }

    public function testNamespace()
    {
        $mock = $this->getClassBuilderMock();
        $mock
            ->expects($this->once())
            ->method('writeLine')
            ->with("namespace test\\phpunit;\n");
        $mock->createNamespace('test\\phpunit');
    }

    public function testEmptyUses()
    {
        $mock = $this->getClassBuilderMock();
        $mock
            ->expects($this->once())
            ->method('writeLine')
            ->with('');
        $mock->createUses([]);
    }

    public function testCreateUses()
    {
        $mock = $this->getClassBuilderMock();
        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with('use test\\aa;');

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with('use ups;');


        $mock->createUses(['test\\aa', 'ups']);
    }

    public function testCreateSimpleClass()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with('/**');

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with(' */');

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with('class PhalconTest');

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with('{');

        $mock->createClass('PhalconTest');

    }


    public function testCreateClassWithExtends()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with('/**');

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with(' */');

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with('class PhalconTest extends Framework');

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with('{');

        $mock->createClass('PhalconTest', 'Framework');
    }


    public function testCreateClassWithInterfaces()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with('class PhalconTest implements IPhalcon, Draw');

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with('{');

        $mock->createClass('PhalconTest', null, ['IPhalcon', 'Draw']);
    }

    public function testCreateClassAllOptions()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with('/**');

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with(' * this is class test');

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with(' */');

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with('abstract class PhalconTest extends Framework implements IPhalcon, Draw');

        $mock
            ->expects($this->at(4))
            ->method('writeLine')
            ->with('{');

        $mock->createClass('PhalconTest', 'Framework', ['IPhalcon', 'Draw'], ['this is class test'], true);
    }


    public function testCloseClass()
    {
        $mock = $this->getClassBuilderMock();
        $mock
            ->expects($this->once())
            ->method('writeLine')
            ->with("}");
        $mock->closeClass();
    }


    public function testAddPropertySimpleName()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with("\t/**");

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with("\t * @var string \$name");

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with("\t */");

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with("\tprivate \$name;\n");

        $mock->addProperty('name');
    }

    public function testAddPropertyAllParameters()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with("\t/**");

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with("\t * this test property");

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with("\t * ");

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with("\t * @var int \$name");

        $mock
            ->expects($this->at(4))
            ->method('writeLine')
            ->with("\t */");

        $mock
            ->expects($this->at(5))
            ->method('writeLine')
            ->with("\tprotected \$name;\n");

        $mock->addProperty('name', 'int', 'protected', ['this test property', '']);
    }

    public function testAddMethodEmpty()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with("\t/**");

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with("\t * @return void");

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with("\t */");

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with("\tpublic function test()");

        $mock
            ->expects($this->at(4))
            ->method('writeLine')
            ->with("\t{");

        $mock
            ->expects($this->at(5))
            ->method('writeLine')
            ->with("\t}");

        $mock->addMethod('test', [], []);
    }

    public function testAddMethodGetter()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with("\t/**");

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with("\t * getter");

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with("\t * @return int");

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with("\t */");

        $mock
            ->expects($this->at(4))
            ->method('writeLine')
            ->with("\tpublic function getTest()");

        $mock
            ->expects($this->at(5))
            ->method('writeLine')
            ->with("\t{");

        $mock
            ->expects($this->at(6))
            ->method('writeLine')
            ->with("\t\treturn \$this->test;");

        $mock
            ->expects($this->at(7))
            ->method('writeLine')
            ->with("\t}");

        $mock->addMethod('getTest', [], ['return $this->test;'], 'int', 'public', ['getter']);
    }


    public function testAddMethodSetter()
    {
        $mock = $this->getClassBuilderMock();

        $mock
            ->expects($this->at(0))
            ->method('writeLine')
            ->with("\t/**");

        $mock
            ->expects($this->at(1))
            ->method('writeLine')
            ->with("\t * setter");

        $mock
            ->expects($this->at(2))
            ->method('writeLine')
            ->with("\t * ");

        $mock
            ->expects($this->at(3))
            ->method('writeLine')
            ->with("\t * @param int \$test");

        $mock
            ->expects($this->at(4))
            ->method('writeLine')
            ->with("\t * @return Framework");

        $mock
            ->expects($this->at(5))
            ->method('writeLine')
            ->with("\t */");

        $mock
            ->expects($this->at(6))
            ->method('writeLine')
            ->with("\tpublic function setTest(\$test)");

        $mock
            ->expects($this->at(7))
            ->method('writeLine')
            ->with("\t{");

        $mock
            ->expects($this->at(8))
            ->method('writeLine')
            ->with("\t\t\$this->test = \$test;");

        $mock
            ->expects($this->at(9))
            ->method('writeLine')
            ->with("\t\t");

        $mock
            ->expects($this->at(10))
            ->method('writeLine')
            ->with("\t\treturn \$this;");

        $mock
            ->expects($this->at(11))
            ->method('writeLine')
            ->with("\t}");

        $mock->addMethod(
            'setTest',
            ['test' => 'int'],
            ['$this->test = $test;', '','return $this;'],
            'Framework',
            'public',
            ['setter', '']
        );
    }


}
 