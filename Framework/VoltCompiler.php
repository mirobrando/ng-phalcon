<?php

namespace mirolabs\phalcon\Framework;


use Phalcon\Mvc\View\Engine\Volt\Compiler;

class VoltCompiler extends Compiler
{
    public function compileFile($path, $compiledPath, $extendsMode = null)
    {
        if (!file_exists($path)) {
            $commonView = $this->getOption('commonView');
            $commonPath = str_replace(
                $this->getDI()->getView()->getViewsDir(),
                $commonView,
                $path
            );
            if (is_readable($commonPath)) {
                $compiledPath = sprintf(
                    "%s%s%s",
                    $this->getOption('compiledPath'),
                    str_replace('/', $this->getOption('compiledSeparator'), $commonPath),
                    $this->getOption('compiledExtension')
                );
                $path = $commonPath;
            }
        }
        return parent::compileFile($path, $compiledPath, $extendsMode);
    }
}
