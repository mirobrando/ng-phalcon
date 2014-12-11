<?php

namespace mirolabs\phalcon\Framework\View;

use mirolabs\phalcon\Framework\Application;
use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Phalcon\Mvc\View;

class VoltCompiler extends Compiler
{
    const OPTION_COMMON_VIEW = 'commonView';
    const OPTION_MODULE_NAME = 'moduleName';
    const OPTION_ENVIRONMENT = 'environment';
    const OPTION_COMPILED_PATH = 'compiledPath';
    const OPTION_COMPILED_SEPARATOR = 'compiledSeparator';
    const OPTION_COMPILED_EXTENSION = 'compiledExtension';
    const OPTION_STAT = 'stat';
    const OPTION_COMPILE_ALWAYS ='compileAlways';
    const DIR_MODULE_OVERWRITTEN = 'modules';


    /**
     * @param string $path
     * @param null $extendsMode
     * @return array|string
     */
    public function compile($path, $extendsMode = null)
    {
        return parent::compile($this->getTemplateFile($path), $extendsMode);
    }


    /**
     * @param $path
     * @return string
     */
    protected function getTemplateFile($path)
    {
        if ($this->getOption(self::OPTION_ENVIRONMENT) == Application::ENVIRONMENT_PROD) {
            return $this->getIndexTemplateFile($path);
        }

        return $this->getCommonTemplateFile($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getIndexTemplateFile($path)
    {
        if ($this->getModuleViewsDir() . 'index.volt' == $path || $path == 'index.volt') {
            return $this->getCommonViewPath($this->getModuleViewsDir() . '/index_deploy.volt');
        }

        return $this->getCommonTemplateFile($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getCommonTemplateFile($path)
    {
        $template = $this->getCommonViewPath($path);
        if (is_readable($template)) {
            return $template;
        }

        return $this->getOverwrittenTemplateFile($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getOverwrittenTemplateFile($path)
    {
        $template = $this->getOverwrittenPath($path);
        if (is_readable($template)) {
            return $template;
        }

        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getCommonViewPath($path)
    {
        return str_replace(
            $this->getModuleViewsDir(),
            $this->getOption(self::OPTION_COMMON_VIEW),
            $path
        );
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getOverwrittenPath($path)
    {
        return str_replace(
            $this->getModuleViewsDir(),
            $this->getOverwrittenDir(),
            $path
        );
    }

    /**
     * @return string
     */
    protected function getOverwrittenDir()
    {
        return sprintf(
            "%s/%s/%s/",
            $this->getOption(self::OPTION_COMMON_VIEW),
            self::DIR_MODULE_OVERWRITTEN,
            $this->getOption(self::OPTION_MODULE_NAME)
        );
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    protected function getModuleViewsDir()
    {
        return $this->getView()->getViewsDir();
    }

    /**
     * @codeCoverageIgnore
     * @return View
     */
    protected function getView()
    {
        return $this->getDI()->get('view');
    }

    /**
     * @codeCoverageIgnore
     * @param $path
     * @param $compiledPath
     * @param $extendsMode
     * @return array|string
     */
    protected function parentwCompileFile($path, $compiledPath, $extendsMode)
    {
        return parent::compileFile($path, $compiledPath, $extendsMode);
    }
}
