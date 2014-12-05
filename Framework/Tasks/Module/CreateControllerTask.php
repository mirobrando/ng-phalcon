<?php

namespace mirolabs\phalcon\Framework\Tasks\Module;

use mirolabs\phalcon\Framework\Tasks\ClassBuilder;
use mirolabs\phalcon\Framework\Tasks\FileBuilder;
use mirolabs\phalcon\Framework\Tasks\Module;

class CreateControllerTask extends Module
{
    const ENTER_CONTROLLER = "Enter the name of the controller";
    const CONTROLLER_EXISTS = "Controller is exists!";
    const ENTER_ACTION = "Enter the name of the action";
    const ENTER_ROUTE = "Enter route for the action";

    /**
     * @param $params
     */
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
        $classBuilder = $this->getControllerFile($projectPath, $moduleName, $controllerName);
        $classBuilder
            ->createPhpFile()
            ->createNamespace(sprintf("namespace %s\\controllers", $moduleName))
            ->createUses(['mirolabs\phalcon\Framework\Module\Controller'])
            ->createClass(ucfirst($controllerName), 'Controller');


        foreach ($actions as $actionName => $route) {
            $classBuilder->addMethod($actionName . 'Action', [], []);
            $this->addRoute($this->getRoutePath($projectPath, $moduleName), $route, $controllerName, $actionName);
            $this->addView($projectPath, $moduleName, $controllerName, $actionName);
        }
        $classBuilder->closeClass();
    }

    /**
     * @param $routePath
     * @param $route
     * @param $controllerName
     * @param $actionName
     */
    private function addRoute($routePath, $route, $controllerName, $actionName)
    {
        #route
        file_put_contents($routePath, sprintf("\n- pattern: %s\n", $route), FILE_APPEND);
        file_put_contents($routePath, "  option:\n", FILE_APPEND);
        file_put_contents(
            $routePath,
            sprintf("    controller: %s\n", mb_strtolower($controllerName, 'UTF-8')),
            FILE_APPEND
        );
        file_put_contents($routePath, sprintf("    action: %s\n", $actionName), FILE_APPEND);

    }

    private function addView($projectPath, $moduleName, $controllerName, $actionName)
    {
        $viewPath =
            $this->getModulePath($projectPath, $moduleName)
            . '/views/' . mb_strtolower($controllerName, 'UTF-8') . '/' . $actionName .'.volt';
        file_put_contents($viewPath, '{% extends "index.volt" %}');
    }



    /**
     * @param $projectPath
     * @param $moduleName
     * @param $controllerName
     * @return ClassBuilder
     */
    private function getControllerFile($projectPath, $moduleName, $controllerName)
    {
        $file = new FileBuilder();
        $file->createFolder(
            $this->getModulePath($projectPath, $moduleName) . '/views/' . mb_strtolower($controllerName, 'UTF-8')
        );
        $file->createFile($this->getControllerPath($projectPath, $moduleName, $controllerName));
        return new ClassBuilder($this->getControllerPath($projectPath, $moduleName, $controllerName));
    }

    /**
     * @param $projectPath
     * @param $moduleName
     * @param $controllerName
     * @return string
     */
    private function getControllerPath($projectPath, $moduleName, $controllerName)
    {
        return
            $this->getModulePath($projectPath, $moduleName) .
            '/controllers/' . ucfirst($controllerName) . 'Controller.php';
    }

    /**
     * @param $projectPath
     * @param $moduleName
     * @return string
     */
    private function getControllerName($projectPath, $moduleName)
    {
        $name = '';
        while ($name == '') {
            $name = $this->validateControllerName(
                $projectPath,
                $moduleName,
                $this->input()->getAnswer(self::ENTER_CONTROLLER)
            );
        }

        return $name;
    }

    /**
     * @param $projectPath
     * @param $moduleName
     * @param $name
     * @return string
     */
    private function validateControllerName($projectPath, $moduleName, $name)
    {
        if (preg_match('/Controller$/', $name)) {
            $name = substr($name, 0, strlen($name) -10);
        }

        if ($name != '') {
            $controllerPath = $this->getControllerPath($projectPath, $moduleName, $name);
            if (file_exists($controllerPath)) {
                $this->output()->writeFormat(self::CONTROLLER_EXISTS, 'error');
            }
        }

        return $name;
    }

    /**
     * @return array
     */
    private function getActions()
    {
        $actions = [];

        $name = 'start';
        while ($name != '') {
            $name = $this->validateAction($this->input()->getAnswer(self::ENTER_ACTION));
            if ($name != '') {
                $actions[$name] = $this->input()->getAnswer(self::ENTER_ROUTE);
            }
        }

        return $actions;
    }

    /**
     * @param $name
     * @return string
     */
    private function validateAction($name)
    {
        if (preg_match('/Action$/', $name)) {
            $name = substr($name, 0, strlen($name) - 6);
        }

        return $name;
    }
}
