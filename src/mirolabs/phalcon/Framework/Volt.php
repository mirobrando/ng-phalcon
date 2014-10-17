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


    public function partial($partialPath)
    {
        $args = func_get_args();

        if (!file_exists($this->getView()->getViewsDir() . $partialPath. '.volt')) {
            $dir = $this->getView()->getViewsDir();
            $this->getView()->setViewsDir($this->getCompiler()->getOption('commonView'));
            call_user_func_array(['parent', 'partial'], $args);
            $this->getView()->setViewsDir($dir);
        } else {
           call_user_func_array(['parent', 'partial'], $args);
        }
    }


}
