<?php

namespace mirolabs\phalcon\Framework\Install;


use mirolabs\phalcon\Framework\Tasks\CreateControllerTask;
use mirolabs\phalcon\Framework\Tasks\CreateModuleTask;

class CreateController
{
    public static function execute()
    {
        $projectPath = getcwd();
        $cModule = new CreateModuleTask();
        $cModule->addModule($projectPath, 'demo');

        $cController = new CreateControllerTask();
        $cController->createController($projectPath, 'demo', 'Default', ['hello' => '/']);

        self::createView($projectPath);
    }


    private static function createView($projectPath)
    {
        $templatePath = $projectPath . '/modules/demo/views/default/hello.volt';
        file_put_contents($templatePath, "\n\n{% block content %}\n", FILE_APPEND);
        file_put_contents($templatePath, "<p>ng-phalcon start!</p>\n", FILE_APPEND);
        file_put_contents($templatePath, "\n\n{% endblock %}\n", FILE_APPEND);
    }
} 