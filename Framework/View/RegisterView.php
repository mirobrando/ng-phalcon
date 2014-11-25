<?php

namespace mirolabs\phalcon\Framework\View;


use Phalcon\DI;
use Phalcon\Mvc\View;

class RegisterView
{
    /**
     * @var DI
     */
    private $dependencyInjection;

    /**
     * @var View
     */
    private $view;

    /**
     * @param View $view
     * @param DI $dependencyInjection
     */
    public function __construct(View $view, DI $dependencyInjection)
    {
        $this->dependencyInjection = $dependencyInjection;
        $this->view = $view;
    }

    /**
     * @param string $moduleName
     * @param string $modulePath
     */
    public function register($moduleName, $modulePath)
    {
        $this->view->setViewsDir($modulePath . '/views/');
        $this->view->registerEngines([".volt" => $this->getVolt($moduleName)]);
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->dependencyInjection->set('view', $this->view);

    }

    /**
     * @param string $moduleName
     * @return Volt
     */
    protected function getVolt($moduleName)
    {
        $config = $this->getConfig();
        $volt = new Volt($this->view, $this->dependencyInjection);
        $volt->setOptions([
            VoltCompiler::OPTION_COMPILED_PATH      => $config->view->compiledPath,
            VoltCompiler::OPTION_COMMON_VIEW        => $config->projectPath . 'common/views/',
            VoltCompiler::OPTION_MODULE_NAME        => $moduleName,
            VoltCompiler::OPTION_COMPILED_SEPARATOR => $config->view->compiledSeparator,
            VoltCompiler::OPTION_ENVIRONMENT        => $config->environment,
            VoltCompiler::OPTION_COMPILED_EXTENSION => $config->view->compiledExtension,
            VoltCompiler::OPTION_COMPILE_ALWAYS     => $config->view->compileAlways,
            VoltCompiler::OPTION_STAT               => $config->view->stat,
        ]);
        $this->createVoltFunctions($volt);
        $this->createVoltFilters($volt);
        $this->createVoltVars($volt);
        return $volt;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param Volt $volt
     */
    protected function createVoltFunctions($volt)
    {
        $dependencyInjection = $this->dependencyInjection;
        $volt->getCompiler()->addFunction('lang', function () use ($dependencyInjection) {
            return '$this->translation->getLang()';
        });
        $volt->getCompiler()->addFunction('trans', function ($resolvedArgs, $exprArgs) use ($dependencyInjection) {
            return sprintf('$this->translation->__get(\'%s\')', $exprArgs[0]['expr']['value']);
        });
        $volt->getCompiler()->addFunction('ng', function ($input) {
            return '"{{".' . $input . '."}}"';
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param Volt $volt
     */
    protected function createVoltFilters($volt)
    {
        $volt->getCompiler()->addFilter('raw', function ($resolvedArgs, $exprArgs) {
            return 'html_entity_decode(' . $resolvedArgs . ')';
        });
    }

    /**
     * @param Volt $volt
     */
    protected function createVoltVars($volt)
    {
        $config = $this->getConfig();
        $volt->getView()->ngAppName = $config->get('ng.app.name');
    }

    /**
     * @return \stdClass
     */
    protected function getConfig()
    {
        return $this->dependencyInjection->get('config');
    }
}
