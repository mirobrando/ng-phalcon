<?php

namespace mirolabs\phalcon\Framework;


use mirolabs\phalcon\Framework\VoltCompiler;

class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    public function getCompiler()
    {
        if (!$this->_compiler) {
            $this->_compiler = new VoltCompiler($this->getView());
            $this->_compiler->setOptions($this->getOptions());
            $this->_compiler->setDI($this->getDI());
        }
        return $this->_compiler;
    }
}
