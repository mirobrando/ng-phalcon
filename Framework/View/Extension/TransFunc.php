<?php
namespace mirolabs\phalcon\Framework\View\Extension;

class TransFunc implements ExFunction
{
    /**
     * @var DI
     */
    private $container;

    private $argument;


    public function __construct($container) {
        $this->container = $container;
    }

    public function call()
    {
        return $this->container->get('translation')->__get($this->argument);
    }

    public function setParams(array $params)
    {
        $this->argument = $params[1][0]['expr']['value'];
    }

    public function getName()
    {
        return 'trans';
    }
}