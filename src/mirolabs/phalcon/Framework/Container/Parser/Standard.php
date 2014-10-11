<?php

namespace mirolabs\phalcon\Framework\Container\Parser;

use mirolabs\phalcon\Framework\Container\Output;
use mirolabs\phalcon\Framework\Container\ParserInterface;

class Standard implements ParserInterface
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
        $this->output->writeLine(sprintf("\t\t\$di->set('%s', [", $this->serviceName));
        $this->output->writeLine(sprintf("\t\t\t'className' => '%s',", $this->className));
        $this->output->writeLine("\t\t\t'arguments' => [");
        $args = [];
        foreach ($this->arguments as $arg) {
            $key = 'value';
            if ($arg['type'] == 'service') {
                $key = 'name';
            }
            $args[] = sprintf(
                "\t\t\t\t['type' => '%s', '%s' => '%s']",
                $arg['type'],
                $key,
                $arg[$key]
            );
        }
        $this->output->writeLine(implode(",\n", $args));
        $this->output->writeLine("\t\t\t]");
        $this->output->writeLine("\t\t]);");
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
     * @param string $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

} 