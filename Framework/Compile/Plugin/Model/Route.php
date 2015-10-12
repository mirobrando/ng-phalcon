<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin\Model;

use Phalcon\Annotations\Annotation;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Route as PluginRoute;
use mirolabs\phalcon\Framework\Logger;
use mirolabs\collection\ArrayList;

class Route {

    /**
     * @var AnnotationParser
     */
    private $annotationParser;
    
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $module;

    
    /**
     * 
     * @param AnnotationParser $parser
     * @param string $className
     * @param string $module
     */
    public function __construct(AnnotationParser $parser, $className, $module) {
        $this->annotationParser = $parser;
        $this->className = $className;
        $this->module = $module;
    }
    
    public function getControllerName() {
        $namespaces = explode('\\', $this->className);
        $controllerClass = $namespaces[count($namespaces)-1];
        if (strpos($controllerClass, 'Controller') == strlen($controllerClass)-10) {
            return strtolower(substr($controllerClass, 0, strlen($controllerClass)-10));
        }
        return strtolower($controllerClass);
    }

    public function getActionName($method) {
        if (strpos($method, 'Action') == strlen($method)-6) {
            return substr($method, 0, strlen($method)-6);
        }
        return $method;
    }

    public function getRoutes() {
        $result = [];
        foreach ($this->annotationParser->getMethods(PluginRoute::ROUTE_ANNOTATION) as $method => $list) {
            foreach ($this->getRoutesForMethod($method, $list) as $data) {
                $result[] = sprintf(
                    "\t\t\$router->add('%s', ['module' => '%s', 'controller' => '%s', 'action' => '%s'], %s);\n",
                    $data['path'], $this->module, $this->getControllerName(), $data['action'],
                    is_null($data['method']) ? 'null' : '\'' . $data['method'] . '\'');
            }
        }
        return $result;
    }
    
    public function getRoutesForMethod($method, ArrayList $list) {
        return $list
            ->filter(function (Annotation $a) { return $a->getName() ==  PluginRoute::ROUTE_ANNOTATION;})
            ->map(function (Annotation $a) use ($method) {return $this->buildRoute($a, $method);})
            ->filter(function ($data) { return count($data) > 0;})
            ->toArray();
    }
    
    public function buildRoute(Annotation $annotation, $method) {
        $path = $annotation->getArgument('path');
        $result = [];
        if ($path) {
            $result['action'] = $this->getActionName($method);
            $result['path'] = $path;
            $result['method'] =  $annotation->getArgument('method');
        } else {
            Logger::getInstance()->warning("Annotation Route must have attribute path @Route(path=/uri)");
        }
        return $result;
    }
            
}
