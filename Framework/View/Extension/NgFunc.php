<?php
namespace mirolabs\phalcon\Framework\View\Extension;

/**
 * @author Miras
 */
class NgFunc implements ExFunction
{
    private $argument;

    public function call()
    {
        return sprintf("{{ %s }}", $this->argument);
    }

    public function setParams(array $params)
    {
        $this->argument = $params[1][0]['expr']['value'];
    }

    public function getName()
    {
        return 'ng';
    }
}