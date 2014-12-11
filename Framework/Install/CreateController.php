<?php

namespace mirolabs\phalcon\Framework\Install;

use mirolabs\phalcon\Framework\Tasks\CreateModuleTask;
use mirolabs\phalcon\Framework\Tasks\Module\CreateControllerTask;

class CreateController
{
    public static function execute()
    {
        $projectPath = getcwd();
        $cModule = new CreateModuleTask();
        $cModule->addModule($projectPath, 'demo', false);

        $cController = new CreateControllerTask();
        $cController->createController($projectPath, 'demo', 'Default', ['hello' => '/']);

        self::createView($projectPath);
    }


    private static function createView($projectPath)
    {
        $templatePath = $projectPath . '/modules/demo/views/default/hello.volt';
        file_put_contents($templatePath, "\n\n{% block content %}\n", FILE_APPEND);
        file_put_contents($templatePath, "<h1>Congratulations!</h1>\n", FILE_APPEND);
        file_put_contents($templatePath, "<h4>You're now flying with ng-Phalcon.</h4>\n", FILE_APPEND);
        file_put_contents($templatePath, "\n\n{% endblock %}\n", FILE_APPEND);
    }
}
