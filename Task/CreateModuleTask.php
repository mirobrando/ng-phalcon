<?php

namespace mirolabs\phalcon\Task;

use mirolabs\phalcon\Framework\Task;

class CreateModuleTask extends Task
{
    const MODULES_DIR     = 'modules';
    const CONTROLLERS_DIR = 'controllers';
    const TRANSLATE_DIR   = 'messages';
    const RESOURCES_DIR   = 'resources';
    const SERVICES_DIR    = 'services';
    const TASKS_DIR       = 'tasks';
    const VIEWS_DIR       = 'views';
    const NG_VIEWS_DIR    = 'ng-views';
    const JS_DIR          = 'js';
    const IMG_DIR         = 'img';
    const CSS_DIR         = 'css';

    public function runAction($params)
    {
        try {
            $projectPath = $params[0];
            $moduleName  = $this->getModuleName($projectPath);
            $this->addModule($projectPath, $moduleName);
        } catch(\Exception $exp) {
            $this->output()->writelnFormat($exp->getMessage(), 'error');
        }
    }

    public function addModule($projectPath, $moduleName, $check = true)
    {
        $moduleDir = $projectPath.'/'.self::MODULES_DIR.'/'.$moduleName;
        $this->createDirs($moduleDir);
        $this->createTranslate($moduleDir.'/'.self::TRANSLATE_DIR);
        $this->createModule($moduleDir, $moduleName);
        $this->addModuleToProject($projectPath, $moduleName, $check);
    }

    private function getModuleName($projectPath)
    {
        $name = '';
        while ($name == '') {
             $name = str_replace(" ", "", $this->input()->getAnswer('Enter the name of the module'));
        }
        $moduleDir = $projectPath.'/'.self::MODULES_DIR.'/'.$name;
        if (file_exists($moduleDir)) {
            throw new \Exception('module is exists!');
        }

        return $name;
    }

    private function createDirs($moduleDir)
    {
        $this->createDir($moduleDir, false);
        $this->createDir($moduleDir.'/'.self::CONTROLLERS_DIR);
        $this->createDir($moduleDir.'/'.self::TASKS_DIR);
        $this->createDir($moduleDir.'/'.self::TRANSLATE_DIR, false);
        $this->createDir($moduleDir.'/'.self::VIEWS_DIR);
        $this->createDir($moduleDir.'/'.self::SERVICES_DIR);
        $this->createDir($moduleDir.'/'.self::RESOURCES_DIR, false);
        $this->createDir($moduleDir.'/'.self::RESOURCES_DIR.'/'.self::NG_VIEWS_DIR);
        $this->createDir($moduleDir.'/'.self::RESOURCES_DIR.'/'.self::JS_DIR);
        $this->createDir($moduleDir.'/'.self::RESOURCES_DIR.'/'.self::IMG_DIR);
        $this->createDir($moduleDir.'/'.self::RESOURCES_DIR.'/'.self::CSS_DIR);
    }

    private function createFile($fileName)
    {
        file_put_contents($fileName, '');
        chmod($fileName, 0777);
    }

    private function writeLine($fileName, $message)
    {
        file_put_contents($fileName, $message."\n", FILE_APPEND);
    }

    private function createTranslate($translateDir)
    {
        $fileName = $translateDir.'/en.php';
        $this->createFile($fileName);
        $this->writeLine($fileName, "<?php\n");
        $this->writeLine($fileName, "return [\n];");
    }

    private function createModule($moduleDir, $moduleName)
    {
        $fileName = $moduleDir.'/Module.php';
        $this->createFile($fileName);
        $this->writeLine($fileName, "<?php\n");
        $this->writeLine($fileName, "namespace ".$moduleName.";\n");
        $this->writeLine($fileName, "class Module extends \mirolabs\phalcon\Framework\Module");
        $this->writeLine($fileName, "{");
        $this->writeLine($fileName, "\tpublic function __construct()");
        $this->writeLine($fileName, "\t{");
        $this->writeLine($fileName, "\t\t".'$this->moduleNamespace =  __NAMESPACE__;');
        $this->writeLine($fileName, "\t\t".'$this->modulePath = __DIR__;');
        $this->writeLine($fileName, "\t}");
        $this->writeLine($fileName, "}");
    }

    private function addModuleToProject($projectPath, $moduleName, $check)
    {
        if ($check) {
            $answer = $this->input()->getAnswer('Do you want add module to project?', 'y', ['y', 'n']);
        }
        if (!$check || $answer == 'y') {
            $this->writeLine($projectPath.'/config/modules.yml', "\n\n".$moduleName.':');
            $this->writeLine($projectPath.'/config/modules.yml', "  className: ".$moduleName.'\\Module');
            $this->writeLine($projectPath.'/config/modules.yml', "  path: modules/".$moduleName."/Module.php");
        }
    }

    private function createDir($dirPath, $hiddenFile = true)
    {
        mkdir($dirPath);
        if ($hiddenFile) {
            file_put_contents($dirPath . "/.ngfile", "git-data");
        }
    }

}