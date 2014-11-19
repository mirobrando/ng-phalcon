<?php

namespace mirolabs\phalcon\Framework\Container\Parser;


use mirolabs\phalcon\Framework\Container\Output;
use mirolabs\phalcon\Framework\Container\ParserInterface;

class Factory implements ParserInterface
{

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $factoryClass;

    /**
     * @var string
     */
    private $factoryMethod;

    /**
     * @var string
     */
    private $arguments = [];

    /**
     * @var Output
     */
    private $output;

    /**
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @return void
     */
    public function writeDefinition()
    {
        $this->output->writeLine(sprintf("\t\t\$di->set('%s',function() use (\$di) {", $this->serviceName));
        $args = [];
        foreach ($this->arguments as $arg) {
            if ($arg['type'] == 'service') {
                $args[] = sprintf("\$di->get('%s')", $arg['name']);
            } else {
                $args[] = sprintf("'%s'", $arg['value']);
            }
        }
        $this->output->writeLine(sprintf(
            "\t\t\t return \$di->get('%s')->%s(%s);",
            $this->factoryClass,
            $this->factoryMethod,
            implode(",", $args)
        ));
        $this->output->writeLine("\t\t});");
    }


    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @param string $factoryClass
     */
    public function setFactoryClass($factoryClass)
    {
        $this->factoryClass = $factoryClass;
    }

    /**
     * @param string $factoryMethod
     */
    public function setFactoryMethod($factoryMethod)
    {
        $this->factoryMethod = $factoryMethod;
    }

    /**
     * @param string $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }
}
