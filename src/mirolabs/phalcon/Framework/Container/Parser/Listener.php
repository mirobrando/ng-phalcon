<?php

namespace mirolabs\phalcon\Framework\Container\Parser;


use mirolabs\phalcon\Framework\Container\Output;
use mirolabs\phalcon\Framework\Container\ParserInterface;

class Listener implements ParserInterface
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
    private $eventName;

    /**
     * @var string
     */
    private $eventMethod;

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
        $this->writeService();
        $this->output->writeLine(sprintf(
            "\t\t\$di->get('listener')->attach('%s', function(\$event, \$component) use (\$di) {",
            $this->eventName
        ));
        $this->output->writeLine(sprintf(
            "\t\t\t\$di->get('%s')->%s(\$event, \$component);",
            $this->serviceName,
            $this->eventMethod
        ));
        $this->output->writeLine("\t\t});");
    }


    private function writeService()
    {
        $standard = new Standard($this->output);
        $standard->setServiceName($this->serviceName);
        $standard->setClassName($this->className);
        foreach ($this->arguments as $arg) {
            $standard->addArgument($arg);
        }
        $standard->writeDefinition();
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
     * @param string $eventMethod
     */
    public function setEventMethod($eventMethod)
    {
        $this->eventMethod = $eventMethod;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @param string $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }
} 