<?php
namespace mirolabs\phalcon\Framework\View\Extension;


class BlockFunc implements ExFunction
{
    private $argument;

    /**
     * @var DI
     */
    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function call()
    {
        $request = $this->container->get('request');
        $address = sprintf("%s://%s%s", $request->getScheme(), $request->getHttpHost(), $this->argument);
        return file_get_contents($address);
    }

    public function setParams(array $params)
    {
        $this->argument = $params[1][0]['expr']['value'];
    }

    public function getName()
    {
        return 'render';
    }
}