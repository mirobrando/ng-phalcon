<?php

namespace tests\mirolabs\phalcon\Framework\View;

use mirolabs\phalcon\Framework\Map;
use mirolabs\phalcon\Framework\View\RegisterView;
use Phalcon\DI;
use Phalcon\Mvc\View;

class RegisterViewTest extends \UnitTestCase
{
    public function testRegisterView()
    {
        $view = \Mockery::mock('Phalcon\Mvc\View');
        $dependencyInjection = new DI();
        $modulePath = 'projectPath/modules';
        $moduleName = 'test';
        $config = new Map();
        $config->set('view', json_encode([
            'compiledPath' => 'compiledPath',
            'compiledSeparator' => 'compiledSeparator',
            'compiledExtension' => '.compile',
            'compileAlways' => true,
            'stat' => 'stat'

        ]));
        $config->set('projectPath', '"projectPath"');
        $config->set('environment', '"dev"');
        $config->set('ng.app.name', '"ngtest"');
        $dependencyInjection->set('config', $config);

        $view
            ->shouldReceive('setViewsDir')
            ->with($modulePath . '/views/')
            ->once();

        $view
            ->shouldReceive('registerEngines')
            ->once();

        $view
            ->shouldReceive('setRenderLevel')
            ->with(View::LEVEL_ACTION_VIEW)
            ->once();

        $view
            ->shouldReceive('setVar')
            ->with('ngAppName', 'ngtest')
            ->once();

        $view->shouldReceive('setDI');

        $registerView = new RegisterView($view, $dependencyInjection);
        $registerView->register($moduleName, $modulePath);

        $view->mockery_verify();

        $this->assertEquals($view, $dependencyInjection->get('view'));


    }
}
