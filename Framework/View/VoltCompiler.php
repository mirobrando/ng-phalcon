<?php

namespace mirolabs\phalcon\Framework\View;

use Phalcon\Mvc\View\Engine\Volt\Compiler;

class VoltCompiler extends Compiler
{
    /**
     * @param string $path
     * @param null $extendsMode
     * @return array|string
     */
    public function compile($path, $extendsMode = null)
    {
        if ($extendsMode) {
            $path = $this->getExtendsTemplate($path);
        }

        return parent::compile($path, $extendsMode);
    }


    protected function getExtendsTemplate($path)
    {
        $this->getManagementPath()->getTemplatePath(
            $this->getView()->getModuleName(),
            $this->getView()->getViewsDir(),
            $path,
            '.volt'
        );
    }

    /**
     * @return ManagementPath
     */
    private function getManagementPath()
    {
        return $this->getDI()->get('managementPath');
    }

    /**
     * @return View
     */
    private function getView()
    {
        return $this->getDI()->get('view');
    }
}
