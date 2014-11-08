<?php

namespace mirolabs\phalcon\Framework\Tasks;

use mirolabs\phalcon\Framework\Task;

class InstallResourcesTask extends Task
{

    public function runAction($params)
    {
        $projectPath = $params[0];
        $modules = $params[1];
        $publicDir = $projectPath . 'public';

        $this->installModule('common', $projectPath. '/common', $publicDir);

        foreach($modules as $name=>$module) {
            $this->installModule($name, $projectPath . dirname($module['path']), $publicDir);
        }

        $this->output()->writelnFormat('resources installed', 'info');
    }




    private function installModule($moduleName, $moduleDir, $publicDir)
    {
        $resourceDir = $moduleDir . '/' . CreateModuleTask::RESOURCES_DIR;
        $cssDir = $resourceDir . '/' . CreateModuleTask::CSS_DIR;
        $jsDir = $resourceDir . '/' . CreateModuleTask::JS_DIR;
        $imgDir = $resourceDir . '/' . CreateModuleTask::IMG_DIR;
        $viewDir  = $resourceDir . '/' . CreateModuleTask::NG_VIEWS_DIR;

        $publicModuleCssDir = $publicDir .'/css/' . $moduleName;
        $publicModuleJsDir = $publicDir .'/js/' . $moduleName;
        $publicModuleViewDir = $publicDir .'/views/' . $moduleName;
        $publicModuleImgDir = $publicDir .'/img/' . $moduleName;

        $this->createSymLink($cssDir, $publicModuleCssDir);
        $this->createSymLink($jsDir, $publicModuleJsDir);
        $this->createSymLink($imgDir, $publicModuleImgDir);
        $this->createSymLink($viewDir, $publicModuleViewDir);
    }


    private function createSymLink($dir, $link)
    {
        if (is_dir($dir)) {
            if (!is_dir($link)) {
                symlink($dir, $link);
            }


        }
    }

}
