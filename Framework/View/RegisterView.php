<?php

namespace mirolabs\phalcon\Framework\View;


use mirolabs\phalcon\Framework\Map;
use Phalcon\DI;
use Phalcon\Mvc\View as PhalconView;

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
    public function __construct($view, DI $dependencyInjection)
    {
        $this->dependencyInjection = $dependencyInjection;
        $this->view = $view;
    }

    /**
     * @param string $moduleName
     * @param string $modulePath
     */
    public function register($moduleName, $modulePath) {
        $this->view->setViewsDir($modulePath . '/views/');
        $this->view->registerEngines([".volt" => $this->getVolt()]);
        $this->view->setLayoutsDir('/../common/views');
        $this->view->setRenderLevel(PhalconView::LEVEL_ACTION_VIEW);
        $this->dependencyInjection->set('view', $this->view);
    }

    /**
     * @return Volt
     */
    protected function getVolt()
    {
        $config = $this->getConfig();
        $volt = new Volt($this->view, $this->dependencyInjection);
        $volt->setOptions([
            'compiledPath'      => $config->view->compiledPath,
            'compiledSeparator' => $config->view->compiledSeparator,
            'compiledExtension' => $config->view->compiledExtension,
            'compileAlways'     => $config->view->compileAlways,
            'stat'              => $config->view->stat,
        ]);
        $this->createVoltFunctions($volt);
        $this->createVoltFilters($volt);
        $this->createVoltVars();
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

        //$volt->getCompiler()->addFunction('dump', 'print_r');
        $volt->getCompiler()->addFunction('pre', function ($input) {
            return '"<pre style=\'font-size:12px\'>" . print_r(' . $input . ', 1) . "</pre>"';
        });
        $volt->getCompiler()->addFunction('js', function ($input) {
            //return 'htmlspecialchars(json_encode(' . $input . '))';
            return 'json_encode(' . $input . ')';
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param Volt $volt
     */
    protected function createVoltFilters($volt)
    {
        $volt->getCompiler()->addFilter('araw', function ($resolvedArgs, $exprArgs) {
            return 'html_entity_decode(' . $resolvedArgs . ')';
        });

        $volt->getCompiler()->addFilter('netto', function ($resolvedArgs, $exprArgs) {
            return 'number_format(' . $resolvedArgs . '["netto"], 2, ",", " ") . " " . ' . $resolvedArgs . '["currency"]';
        });

        $volt->getCompiler()->addFilter('brutto', function ($resolvedArgs, $exprArgs) {
            return 'number_format(' . $resolvedArgs . '["brutto"], 2, ",", " ") . " " . ' . $resolvedArgs . '["currency"]';
        });

        $volt->getCompiler()->addFilter('price', function ($resolvedArgs, $exprArgs) {
            return 'number_format(' . $resolvedArgs . ', 2, ",", " ")';
        });
    }

    /**
     */
    protected function createVoltVars()
    {
        $config = $this->getConfig();
        $this->view->setVar('ngAppName', $config->get('ng.app.name'));
    }

    /**
     * @return Map
     */
    protected function getConfig()
    {
        return $this->dependencyInjection->get('config');
    }
}
