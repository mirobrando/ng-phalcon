<?php
namespace mirolabs\phalcon\Framework\View\Extension;

use Phalcon\Mvc\View as PhalconView;

class JsonEncodeFunc implements ExFunction
{
    private $argument;

    /**
     * @va
     * @var typer PhalconView
     */
    private $view;

    public function __construct(PhalconView $view) {
        $this->view = $view;
    }

    public function call()
    {
        return json_encode($this->argument);
    }

    public function setParams(array $params)
    {
        if ( preg_match('/^\$.+/', $params[0], $matches)) {
            $varName = $params[1][0]['expr']['value'];
            $this->argument = $this->view->$varName;
        } else {
            $this->argument = $params[1][0]['expr']['value'];
        }
    }

    public function getName()
    {
        return 'json';
    }
}