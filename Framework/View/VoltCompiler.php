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
        $tmp_path = $this->getExtendsTemplate($path);
        if (file_exists($tmp_path)) {
            $path = $tmp_path;
        }

        return parent::compile($path, $extendsMode);
    }


    protected function getExtendsTemplate($path)
    {
        $data = explode('.', str_replace($this->getView()->getViewsDir(), "", $path));
        return $this->getManagementPath()->getTemplatePath(
            $this->getView()->getModuleName(),
            $this->getView()->getViewsDir(),
            $data[0],
            '.' . $data[1]
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
