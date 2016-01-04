<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin\Model;

use Phalcon\Annotations\Annotation;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Task as PluginTask; 
use mirolabs\phalcon\Framework\Logger;
use mirolabs\collection\ArrayList;

class Task {

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
    public function __construct(AnnotationParser $parser, $className) 
    {
        $this->annotationParser = $parser;
        $this->className = $className;
    }

    public function getTasks() {
        $result = [];
        foreach ($this->annotationParser->getMethods(PluginTask::METHOD_ANNOTATION) as $method => $list) {
            $taskAnnotation = $this->findTaskAnnotation($list);
            $command = $taskAnnotation->getArgument(PluginTask::METHOD_ANNOTATION_COMMAND);
            $description = $taskAnnotation->getArgument(PluginTask::METHOD_ANNOTATION_DESCRIPTION);
            $group = $taskAnnotation->getArgument(PluginTask::METHOD_ANNOTATION_GROUP);
            if (strpos($method, 'Action') == strlen($method)-6) {
                $action = substr($method, 0, strlen($method)-6);
            } else {
                Logger::getInstance()->warning("Method must have Action sufix");
                continue;
            }            
            if (is_null($command)) {
                Logger::getInstance()->warning("Annotation Task must have attribute command @Task(command='<command_name>')");
                continue;
            }
            $line = sprintf("\t\t\t['%s']=>['class'=>'%s','action'=>'%s','description'=>'%s','group' => '%s']\n",
                    $command, $this->className, $action, $description, $group);
            $result[] = $line;
        }
        return $result;
    }
    
    /**
     * 
     * @param ArrayList $list
     * @return Annotation
     */
    private function findTaskAnnotation(ArrayList $list) {
        return $list->find(function (Annotation $annotation) { return $annotation->getName() == PluginTask::METHOD_ANNOTATION;});
    }
    
}
