<?php
namespace mirolabs\phalcon\Framework\View\Extension;

class LangFunc implements ExFunction
{
    /**
     * @var DI
     */
    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function call()
    {
        return $this->container->get('translation')->getLang();
    }

    public function setParams(array $params)
    {
        $this->argument = $params[1][0]['expr']['value'];
    }

    public function getName()
    {
        return 'lang';
    }
}