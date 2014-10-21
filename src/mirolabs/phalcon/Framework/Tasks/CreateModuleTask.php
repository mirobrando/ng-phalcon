<?php

namespace mirolabs\phalcon\Framework\Tasks;


use mirolabs\phalcon\Framework\Task;

class CreateModuleTask extends Task
{

    const MODULES_DIR = 'modules';
    const CONTROLLERS_DIR = 'controllers';
    const CONFIG_DIR = 'config';
    const TRANSLATE_DIR = 'messages';
    const RESOURCES_DIR = 'resources';
    const SERVICES_DIR = 'services';
    const TASKS_DIR = 'tasks';
    const VIEWS_DIR = 'views';
    const NG_VIEWS_DIR  = 'ng-views';
    const JS_DIR  = 'js';
    const IMG_DIR  = 'img';
    const CSS_DIR  = 'css';
    const ROUTE_FILE = 'route.yml';
    const SERVICES_FILE = 'services.yml';


    public function runAction($params)
    {
        $projectPath = $params[0];
        $moduleName = $this->getModuleName($projectPath);
        if ($moduleName === false) {
            return;
        }
        $moduleDir = $projectPath . '/' . self::MODULES_DIR . '/' . $moduleName;
        $this->createDirs($moduleDir);
        $this->createRoute($moduleDir . '/'. self::CONFIG_DIR);
        $this->createServices($moduleDir . '/'. self::CONFIG_DIR);
        $this->createTranslate($moduleDir . '/'. self::TRANSLATE_DIR);
        $this->createModule($moduleDir, $moduleName);
        $this->addModuleToProject($projectPath, $moduleName);

    }


    private function getModuleName($projectPath)
    {
        $name = '';
        while($name == '') {
            $name = $this->input()->getAnswer('Enter the name of the module');
        }
        $moduleDir = $projectPath . '/' . self::MODULES_DIR . '/' . $name;
        if (file_exists($moduleDir)) {
            $this->output()->writeFormat('module is exists!', 'error');
            return false;
        }

        return $name;
    }

    private function createDirs($moduleDir)
    {
        mkdir($moduleDir);
        mkdir($moduleDir . '/'. self::CONTROLLERS_DIR);
        mkdir($moduleDir . '/'. self::TASKS_DIR);
        mkdir($moduleDir . '/'. self::CONFIG_DIR);
        mkdir($moduleDir . '/'. self::TRANSLATE_DIR);
        mkdir($moduleDir . '/'. self::VIEWS_DIR);
        mkdir($moduleDir . '/'. self::SERVICES_DIR);
        mkdir($moduleDir . '/'. self::RESOURCES_DIR);
        mkdir($moduleDir . '/'. self::RESOURCES_DIR . '/' . self::NG_VIEWS_DIR);
        mkdir($moduleDir . '/'. self::RESOURCES_DIR . '/' . self::JS_DIR);
        mkdir($moduleDir . '/'. self::RESOURCES_DIR . '/' . self::IMG_DIR);
        mkdir($moduleDir . '/'. self::RESOURCES_DIR . '/' . self::CSS_DIR);

    }

    private function createFile($fileName)
    {
        file_put_contents($fileName,'');
        chmod($fileName,0777);
    }

    private function writeLine($fileName, $message)
    {
        file_put_contents($fileName, $message . "\n", FILE_APPEND);
    }


    private function createRoute($configDir)
    {
        $fileName = $configDir . '/' .self::ROUTE_FILE;
        $this->createFile($fileName);
        $this->writeLine($fileName, '#route.yml');
        $this->writeLine($fileName, '#- pattern: /{language:[a-z]{2}}/dictionary/header');
        $this->writeLine($fileName, '#  method: GET');
        $this->writeLine($fileName, '#  option:');
        $this->writeLine($fileName, '#    controller: Rest');
        $this->writeLine($fileName, '#    action: header');
    }

    private function createServices($configDir)
    {
        $fileName = $configDir . '/' .self::SERVICES_FILE;
        $this->createFile($fileName);
        $this->writeLine($fileName, '#services.yml');
        $this->writeLine($fileName, "parameters:\n");
        $this->writeLine($fileName, "services:\n");
        $this->writeLine($fileName, "tasks:");
    }

    private function createTranslate($translateDir)
    {
        $fileName = $translateDir . '/en.php' ;
        $this->createFile($fileName);
        $this->writeLine($fileName, "<?php\n");
        $this->writeLine($fileName, "\$messages = [\n];");
    }

    private function createModule($moduleDir, $moduleName)
    {
        $fileName = $moduleDir . '/Module.php' ;
        $this->createFile($fileName);
        $this->writeLine($fileName, "<?php\n");
        $this->writeLine($fileName, "namespace " . $moduleName . ";\n");
        $this->writeLine($fileName, "class Module extends \mirolabs\phalcon\Framework\Module");
        $this->writeLine($fileName, "{");
        $this->writeLine($fileName, "\tpublic function __construct()");
        $this->writeLine($fileName, "\t{");
        $this->writeLine($fileName, "\t\t" . '$this->moduleNamespace =  __NAMESPACE__;');
        $this->writeLine($fileName, "\t\t" . '$this->modulePath = __DIR__;');
        $this->writeLine($fileName, "\t}");
        $this->writeLine($fileName, "}");
    }


    private function addModuleToProject($projectPath, $moduleName)
    {
        $answer = $this->input()->getAnswer('Do you want add module to project?', 'y', ['y', 'n']);
        if ($answer == 'y') {
            $this->writeLine($projectPath . '/config/modules.yml', "\n" . $moduleName);
            $this->writeLine($projectPath . '/config/modules.yml', "  className: " . $moduleName . '\\Module\\');
            $this->writeLine($projectPath . '/config/modules.yml', "  path: modules/" . $moduleName . "/Module.php");
        }
    }
}