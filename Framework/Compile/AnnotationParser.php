<?php

namespace mirolabs\phalcon\Framework\Compile;

use Phalcon\Annotations\Reflection;
use Phalcon\Annotations\Annotation;
use mirolabs\collection\ArrayList;


class AnnotationParser {

    /**
     * @var Reflection 
     */
    private $reflection;
    
    public function __construct(Reflection $reflection)
    {
        $this->reflection = $reflection;
    }
    
    /**
     * 
     * @param string $annotationName
     * @return boolean
     */
    public function isExistsAnnotationClass($annotationName) {
        return $this->getExistsAnnotationClass($annotationName) != null;
    }
    
    /**
     * 
     * @param string $annotationName
     * @return Annotation
     */
    public function getExistsAnnotationClass($annotationName) {
        if($this->reflection->getClassAnnotations() !== false) {
            $list = ArrayList::create($this->reflection->getClassAnnotations()->getAnnotations());
            return $list->find(function (Annotation $annotation) use ($annotationName) {
                return $annotation->getName() == $annotationName; });
        }
        return null;
    }
    
    
    public function getProperties($annotationName) {
        $result = [];
        if ($this->reflection->getPropertiesAnnotations() !== false) {
            foreach($this->reflection->getPropertiesAnnotations() as $field=>$annotations) {
                $list = ArrayList::create($annotations->getAnnotations());
                if ($list->find(function (Annotation $annotation) use ($annotationName) {
                        return $annotation->getName() == $annotationName; }) != null) {
                    $result[$field] = $list;        
                }
            }
        }
        return $result;
    }
}
