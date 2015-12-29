<?php
namespace mirolabs\phalcon\Framework\View\Extension;

use Phalcon\Mvc\View as PhalconView;

/**
 * @author Miras
 */
class PreFunc implements ExFunction
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
        return sprintf("<pre style='font-size:12px'>%s</pre>", print_r($this->argument, 1));
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
        return 'pre';
    }
}