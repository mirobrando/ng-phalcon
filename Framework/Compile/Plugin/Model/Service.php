<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin\Model;

use Phalcon\Annotations\Annotation;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Service as PluginService; 
use mirolabs\phalcon\Framework\Logger;
use mirolabs\collection\ArrayList;

class Service {
    
    /**
     *
     * @var AnnotationParser
     */
    protected $annotationParser;
    
    /**
     *
     * @var string
     */
    protected $className;


    /**
     * @var Closure
     */
    private $getServiceName;
    
    /**
     * @var Closure
     */
    private $getParameterValue;
    
    /**
     * 
     * @param AnnotationParser $parser
     * @param string $className
     */
    public function __construct(AnnotationParser $parser, $className, $getServiceName, $getValue) 
    {
        $this->annotationParser = $parser;
        $this->className = $className;
        $this->getServiceName = $getServiceName;
        $this->getParameterValue = $getValue;
    }
    
    public function getClassName()
    {
        return $this->className;
    }

    public function getServiceName()
    {
        $annotation = $this->annotationParser->getExistsAnnotationClass(PluginService::CLASS_ANNOTATION);
        $serviceName = $annotation->getArgument(0); 
        if (is_null($serviceName)) {
            $serviceName = str_replace('\\', '.', strtolower($this->className));
        }
        return $serviceName;
    }

        
    public function getServiceCache() 
    {
        $result = $this->getHeader();
        $result .= $this->getProperties();
        $result .= "\t\t]));";
        return $result;
    }
    
    
    private function getHeader() 
    {
        $result = sprintf("\t\t\$di->setRaw('%s', new \mirolabs\phalcon\Framework\Container\Service('%s', [\n", 
                $this->getServiceName(), $this->getServiceName());
        $result .= sprintf("\t\t\t'className' => '%s',\n", $this->className);
        return $result;
    }
    
    private function getProperties()
    {
        $result = "\t\t\t'properties' => [\n";
        $properties = [];
        foreach ($this->annotationParser->getProperties(PluginService::PROPERTY_ANNOTATION) as $field => $list) {
            $property = $this->getPropertyService($field, $list);
            if ($property != "") {
                $properties[] = sprintf("\t\t\t\t[\n%s\n\t\t\t\t]", $property);
            }
        }
        foreach ($this->annotationParser->getProperties(PluginService::PROPERTY_ANNOTATION_VALUE) as $field => $list) {
            $property = $this->getPropertyValue($field, $list);
            if ($property != "") {
                $properties[] = sprintf("\t\t\t\t[\n%s\n\t\t\t\t]", $property);
            }
        }
        $result .= implode(",\n", $properties);
        $result .= "\n\t\t\t]\n";
        return $result;
    }
    
    private function getPropertyService($field, ArrayList $list) 
    {
        $property = $list->find(function(Annotation $annotation) { return $annotation->getName() ==  PluginService::PROPERTY_ANNOTATION;});
        $serviceName = $property->getArgument('service');
        $definition = sprintf("\t\t\t\t\t'name' => '%s',\n", $field);
        $pattern = "\t\t\t\t\t'value' => ['type' => 'service', 'name' => '%s']";
        if (!is_null($serviceName)) {
            return $definition . sprintf($pattern, $serviceName);
        }

        $serviceType = $property->getArgument('type');
        if (!is_null($serviceType)) {
            $c = $this->getServiceName;
            $serviceName = $c($serviceType);
            if ($serviceName) {
                return $definition . sprintf($pattern, $c($serviceType));
            } else {
                Logger::getInstance()->warning('In Annotation Inject is invalid type %s', $serviceType);
                return "";
            }
        }
        Logger::getInstance()->warning("Annotation Inject required attribute service or type");
        return "";
    }

    private function getPropertyValue($field, ArrayList $list) 
    {
        $property = $list->find(function(Annotation $annotation) { return $annotation->getName() ==  PluginService::PROPERTY_ANNOTATION_VALUE;});
        $valueName = $property->getArgument(0);
        if (!is_null($valueName)) {
            $definition = sprintf("\t\t\t\t\t'name' => '%s',\n", $field);
            $pattern = "\t\t\t\t\t'value' => ['type' => 'parameter', 'value' => '%s']";
            $c = $this->getParameterValue;
            return $definition . sprintf($pattern, $c($valueName));            
        }
        Logger::getInstance()->warning("Annotation Value required name");
        return "";
    }
    
}
