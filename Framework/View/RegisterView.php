<?php

namespace mirolabs\phalcon\Framework\View;


use mirolabs\phalcon\Framework\Map;
use Phalcon\DI;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;

class RegisterView
{
    /**
     * @var DI
     */
    private $dependencyInjection;

    /**
     * @var PhalconView
     */
    private $view;


    /**
     * @var PhalconVolt
     */
    private $volt;

    
    /**
     * @param View $view
     * @param DI $dependencyInjection
     */
    public function __construct(PhalconView $view, DI $dependencyInjection)
    {
        $this->dependencyInjection = $dependencyInjection;
        $this->view = $view;
    }

    /**
     * @param string $moduleName
     * @param string $modulePath
     */
    public function register($moduleName, $modulePath) {
        $pPath = $this->dependencyInjection->get('config')->projectPath;
        $pDir = substr($pPath,0,strlen($pPath)-10);
        $relModulePath = substr($modulePath, strlen($pDir));
        $x = implode('/', array_map(function() {return '..';}, explode('/', str_replace('//','/', $relModulePath))));
        $this->view->setMainView('/../'. $x . '/common/views/index');
        $this->view->setPartialsDir('/../'. $x . '/common/views/partial/');
        $this->view->setViewsDir($modulePath . '/views/');
        $this->registerEngine();
        $this->dependencyInjection->set('view', $this->view);
    }

    /**
     * @return Volt
     */
    protected function registerEngine() {
        $config = $this->getConfig();
        $this->volt = new PhalconVolt($this->view, $this->dependencyInjection);
        if (!is_dir($config->view->compiledPath)) {
            mkdir($config->view->compiledPath, 0777, true);
        }
        $this->volt->setOptions([
            'compiledPath'      => $config->view->compiledPath,
            'compiledSeparator' => $config->view->compiledSeparator,
            'compiledExtension' => $config->view->compiledExtension,
            'compileAlways'     => $config->view->compileAlways,
            'stat'              => $config->view->stat,
        ]);
        $this->createVoltFunctions();
        $this->createVoltFilters();
        $this->createVoltVars();
        $this->view->registerEngines([".volt" => $this->volt]);
    }

    public function addFunction(Extension\ExFunction $extension)
    {
        $this->volt->getCompiler()->addFunction(
            $extension->getName(),
            function() use ($extension) {
                $this->view->setVar('func_extension_' . $extension->getName(), $extension);
                $extension->setParams(func_get_args());
                return '$this->getView()->getVar(\'func_extension_' . $extension->getName() . '\')->call();';
            }
        );
    }


    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param Volt $volt
     */
    protected function createVoltFunctions() {

        $dependencyInjection = $this->dependencyInjection;

        /**
        $this->volt->getCompiler()->addFunction('lang', function () use ($dependencyInjection) {
            //return '"<pre style=\'font-size:12px\'>" . print_r(' . $this . ', 1) . "</pre>"';
            return '$this->translation->getLang()';
        });
        $this->volt->getCompiler()->addFunction('trans', function ($resolvedArgs, $exprArgs) use ($dependencyInjection) {
            return sprintf('$this->translation->__get(\'%s\')', $exprArgs[0]['expr']['value']);
        });*/

        $this->addFunction(new Extension\JsonEncodeFunc($this->view));
        $this->addFunction(new Extension\PreFunc($this->view));
        $this->addFunction(new Extension\NgFunc());
        $this->addFunction(new Extension\LangFunc($this->dependencyInjection));
        $this->addFunction(new Extension\TransFunc($this->dependencyInjection));
        $this->addFunction(new Extension\BlockFunc($this->dependencyInjection));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @param Volt $volt
     */
    protected function createVoltFilters() {
        /**
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

         */
    }

    /**
     */
    protected function createVoltVars() {
        $config = $this->getConfig();
        $this->view->setVar('ngAppName', $config->get('ng.app.name'));
    }

    /**
     * @return Map
     */
    protected function getConfig() {
        return $this->dependencyInjection->get('config');
    }
}
