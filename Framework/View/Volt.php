<?php

namespace mirolabs\phalcon\Framework\View;

use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;

class Volt extends PhalconVolt
{
    /**
     * @return VoltCompiler
     */
    public function getCompiler()
    {
        if (!$this->_compiler) {
            $this->_compiler = new VoltCompiler();
            $this->_compiler->setOptions($this->getOptions());
            $this->_compiler->setDI($this->getDI());
        }

        return $this->_compiler;
    }
}
