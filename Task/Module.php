<?php

namespace mirolabs\phalcon\Task;

use mirolabs\phalcon\Framework\Task;

class Module extends Task
{
    const MODULES_IS_EMPTY    = "Project doesn't have its own modules";
    const MODULE_IS_NOT_EXIST = "Module isn't exists";
    const ENTER_MODULE        = "Enter the name of the module";

    /**
     * @param $projectPath
     * @return bool | string
     */
    protected function getModuleName($projectPath)
    {
        $modules = $this->getModules($projectPath);
        if (empty($modules)) {
            $this->output()->writelnFormat(self::MODULES_IS_EMPTY, 'error');
            return false;
        }

        $name = $this->input()->getAnswer(self::ENTER_MODULE, '', $modules);
        if (!file_exists($this->getModulePath($projectPath, $name))) {
            $this->output()->writelnFormat(self::MODULE_IS_NOT_EXIST, 'error');
            return false;
        }

        $this->output()->writelnFormat('');
        return $name;
    }

    /**
     * @param string $projectPath
     * @return string
     */
    protected function getModulesPath($projectPath)
    {
        return $projectPath.'/'.CreateModuleTask::MODULES_DIR;
    }

    /**
     * @param string $projectPath
     * @param string $moduleName
     * @return string
     */
    protected function getModulePath($projectPath, $moduleName)
    {
        return $this->getModulesPath($projectPath).'/'.$moduleName;
    }

    /**
     * @param $projectPath
     * @return array
     */
    protected function getModules($projectPath)
    {
        $modulesDir = $this->getModulesPath($projectPath).'/';
        $modules    = [];
        if ($handle     = opendir($modulesDir)) {
            while (false !== ($module = readdir($handle))) {
                if (!in_array($module, ['.', '..']) && is_dir($modulesDir.$module)) {
                    $modules[] = $module;
                }
            }
            closedir($handle);
        }

        return $modules;
    }

    /**
     * @param $projectPath
     * @param $moduleName
     * @return string
     */
    protected function getRoutePath($projectPath, $moduleName)
    {
        return $projectPath.'/'.CreateModuleTask::MODULES_DIR.'/'.$moduleName.'/config/route.yml';
    }
}