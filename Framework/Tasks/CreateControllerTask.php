<?php

namespace mirolabs\phalcon\Framework\Tasks;


use mirolabs\phalcon\Framework\Task;

class CreateControllerTask extends Task
{
    public function runAction($params)
    {
        $projectPath = $params[0];
        $moduleName = $this->getModuleName($projectPath);
        if ($moduleName !== false) {
            $controllerName = $this->getControllerName($projectPath, $moduleName);
            $actions = $this->getActions();
            $this->createController($projectPath, $moduleName, $controllerName, $actions);
        }
    }


    public function createController($projectPath, $moduleName, $controllerName, $actions)
    {
        $controllerPath = $this->getControllerPath($projectPath, $moduleName, $controllerName);
        file_put_contents($controllerPath, "<?php\n\n");
        file_put_contents($controllerPath, sprintf("namespace %s\\controllers;\n\n", $moduleName), FILE_APPEND);
        file_put_contents($controllerPath, "use Phalcon\\Mvc\\Controller;\n\n", FILE_APPEND);
        file_put_contents(
            $controllerPath,
            sprintf("class %sController extends Controller\n", $controllerName),
            FILE_APPEND
        );
        file_put_contents($controllerPath, "{\n", FILE_APPEND);
        foreach ($actions as $actionName => $route) {
            $this->addAction($projectPath, $moduleName, $controllerName, $actionName, $route);
        }
        file_put_contents($controllerPath, "}\n", FILE_APPEND);
    }

    public function addAction($projectPath, $moduleName, $controllerName, $actionName, $route)
    {
        $controllerPath = $this->getControllerPath($projectPath, $moduleName, $controllerName);
        $routePath = $this->getRoutePath($projectPath, $moduleName);

        file_put_contents(
            $controllerPath,
            sprintf("\n\tpublic function %sAction()\n", $actionName),
            FILE_APPEND
        );
        file_put_contents($controllerPath, "\t{\n\n", FILE_APPEND);
        file_put_contents($controllerPath, "\t}\n\n", FILE_APPEND);

        file_put_contents($routePath, sprintf("\n- pattern: %s\n", $route) , FILE_APPEND);
        file_put_contents($routePath, "  option:\n" , FILE_APPEND);
        file_put_contents($routePath, sprintf("    controller: %s\n", $controllerName) , FILE_APPEND);
        file_put_contents($routePath, sprintf("    action: %s\n", $actionName) , FILE_APPEND);

    }


    private function getModuleName($projectPath)
    {
        $name = '';
        while($name == '') {
            $name = $this->input()->getAnswer('Enter the name of the module');
        }
        $moduleDir = $this->getModulePath($projectPath, $name);
        if (file_exists($moduleDir)) {
            return $name;
        }

        $this->output()->writeFormat('module isn\'t exists!', 'error');
        return false;
    }


    private function getControllerName($projectPath, $moduleName)
    {
        $name = '';
        while($name == '') {
            $name = $this->input()->getAnswer('Enter the name of the controller');
            if (!preg_match('/Controller$/', $name)) {
                $name = substr($name, 0 , strlen($name) -10);
            }

            if ($name != '') {
                $controllerPath = $this->getControllerPath($projectPath, $moduleName, $name);
                if (file_exists($controllerPath)) {
                    $this->output()->writeFormat('controller is exists!', 'error');
                }
            }
        }

        return $name;
    }


    private function getActions()
    {
        $actions = [];

        $name = 'start';
        while($name != '') {
            $name = $this->input()->getAnswer('Enter the name of the action');
            if (!preg_match('/Action/', $name)) {
                $name = substr($name, 0 , strlen($name) -10);
            }

            if ($name != '') {
                $actions[$name] = $this->input()->getAnswer('Enter route of the action');
            }
        }

        return $actions;
    }

    private function getModulePath($projectPath, $moduleName)
    {
        return $projectPath . '/' .  CreateModuleTask::MODULES_DIR . '/' . $moduleName;
    }

    private function getControllerPath($projectPath, $moduleName, $controllerName)
    {
        return $this->getModulePath($projectPath, $moduleName) . '/controllers/' . $controllerName . 'Controller';
    }

    private function getRoutePath($projectPath, $moduleName)
    {
        return $projectPath . '/' .  CreateModuleTask::MODULES_DIR . '/' . $moduleName . '/config/route.yml';
    }



} 