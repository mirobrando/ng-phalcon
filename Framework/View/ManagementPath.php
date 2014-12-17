<?php

namespace mirolabs\phalcon\Framework\View;


use mirolabs\phalcon\Framework\Application;

class ManagementPath
{
    const DIR_MODULE_OVERWRITTEN = 'modules/';

    /**
     * @var string
     */
    private $commonPath;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param string $commonPath
     * @param string $environment
     */
    public function __construct($commonPath, $environment)
    {
        $this->commonPath = $commonPath;
        $this->environment = $environment;
    }

    /**
     * @param string $moduleName
     * @param string $moduleViewsDir
     * @param string $template
     * @param string $extension
     * @return string
     */
    public function getTemplatePath($moduleName, $moduleViewsDir, $template, $extension)
    {
        if ($this->environment == Application::ENVIRONMENT_PROD) {
            return $this->getIndexTemplateFile($moduleName, $moduleViewsDir, $template, $extension);
        }

        return $this->getCommonTemplateFile($moduleName, $moduleViewsDir, $template, $extension);
    }

    /**
     * @param string $moduleName
     * @param string $moduleViewsDir
     * @param string $template
     * @param string $extension
     * @return string
     */
    protected function getIndexTemplateFile($moduleName, $moduleViewsDir, $template, $extension)
    {
        if ($template == 'index') {
            return $this->commonPath . 'index_deploy' . $extension;
        }

        return $this->getCommonTemplateFile($moduleName, $moduleViewsDir, $template, $extension);
    }

    /**
     * @param string $moduleName
     * @param string $moduleViewsDir
     * @param string $template
     * @param string $extension
     * @return string
     */
    protected function getCommonTemplateFile($moduleName, $moduleViewsDir, $template, $extension)
    {
        $pathTemplate = $this->commonPath . $template . $extension;
        if (is_readable($pathTemplate)) {
            return $pathTemplate;
        }

        return $this->getOverwrittenTemplateFile($moduleName, $moduleViewsDir, $template, $extension);
    }

    /**
     * @param string $moduleName
     * @param string $moduleViewsDir
     * @param string $template
     * @param string $extension
     * @return string
     */
    protected function getOverwrittenTemplateFile($moduleName, $moduleViewsDir, $template, $extension)
    {
        $pathTemplate = $this->commonPath . self::DIR_MODULE_OVERWRITTEN . $moduleName . '/' . $template . $extension;
        if (is_readable($pathTemplate)) {
            return $pathTemplate;
        }

        return $moduleViewsDir . $template . $extension;
    }
}
