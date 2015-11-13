<?php

namespace mirolabs\phalcon\Framework\Module;

use Phalcon\Annotations\Collection;
use Phalcon\Mvc\Controller as PhalconController;
use mirolabs\phalcon\Framework\Compile\Plugin\Service;
use mirolabs\phalcon\Framework\Logger;

class Controller extends PhalconController {
    
    use \mirolabs\phalcon\Framework\Compile\Plugin\Config;
    
    public function onConstruct() {
        $annotations = $this->getDI()->get('annotations')->get($this)->getPropertiesAnnotations();
        if (is_array($annotations)) {
            foreach ($annotations as $propertyName => $propertyAnnotation) {
                $this->setPropertyValue($propertyName, $propertyAnnotation);
            }
        }
    }

    protected function getConfig() {
        return $this->getDI()->get('config');
    }
    
    /**
     * @param string $propertyName
     * @param Collection $annotations
     */
    private function setPropertyValue($propertyName, Collection $annotations)
    {
        if ($annotations->has(Service::PROPERTY_ANNOTATION_VALUE)) {
            $valueName = $annotations->get(Service::PROPERTY_ANNOTATION_VALUE)->getArgument(0);
            if (!is_null($valueName)) {
                $this->propertyName = $this->getValue($valueName);
            }
        } else {
            $this->setPropertyService($propertyName, $annotations);
        }
    }

    /**
     * @param string $propertyName
     * @param Collection $annotations
     */
    private function setPropertyService($propertyName, Collection $annotations) {
        
        if ($annotations->has(Service::PROPERTY_ANNOTATION)) {
            $annProperty = $annotations->get(Service::PROPERTY_ANNOTATION);
            if (!is_null($serviceName)) {
                $this->$propertyName = $this->getDI()->get($serviceName);
            } else {
                Logger::getInstance()->warning("Annotation Inject in Controller required attribute service");
            }
        }
    }
}
