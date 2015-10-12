<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin\Model;

use Phalcon\Annotations\Annotation;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Listener as PluginListener;
use mirolabs\phalcon\Framework\Compile\Plugin\Service as PluginService;
use mirolabs\phalcon\Framework\Logger;
use mirolabs\collection\ArrayList;

class Listener {
    /**
     *
     * @var AnnotationParser
     */
    private $annotationParser;
    
    /**
     *
     * @var string
     */
    private $className;
    
        /**
     * 
     * @param AnnotationParser $parser
     * @param string $className
     */
    public function __construct(AnnotationParser $parser, $className) {
        $this->annotationParser = $parser;
        $this->className = $className;
    }
    
    public function getServiceName() {
        $annotation = $this->annotationParser->getExistsAnnotationClass(PluginService::CLASS_ANNOTATION);
        $serviceName = $annotation->getArgument(0); 
        if (is_null($serviceName)) {
            $serviceName = str_replace('\\', '.', $this->className);
        }
        return $serviceName;
    }
    
    public function getListeners() {
        $result = [];
        foreach ($this->annotationParser->getMethods(PluginListener::EVENT_ANNOTATION) as $method => $list) {
            $eventName = $this->findEventName($list);
            if (is_null($eventName)) {
                \mirolabs\phalcon\Framework\Logger::getInstance()->warning("Annotation Listener must have event name @Listener('event.name)'");
                continue;
            }
            
            $line = sprintf("\t\t\$di->get('listener')->attach('%s', function(\$event, \$component, \$param) use (\$di) {\n", $eventName);
            $line .= sprintf("\t\t\t\$di->get('%s')->%s(\$event, \$component, \$param);\n\t\t});\n", 
                    $this->getServiceName(), $method);
            $result[] = $line;
        }
        return $result;
    }
    
    private function findEventName(ArrayList $list) {
        return $list->find(function (Annotation $annotation) { return $annotation->getName() == PluginListener::EVENT_ANNOTATION;})
            ->getArgument(0); 
    }
}
