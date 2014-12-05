<?php

namespace mirolabs\phalcon\Framework\Module;

use Phalcon\Annotations\Collection;
use Phalcon\Mvc\Controller as PhalconController;

class Controller extends PhalconController
{
    public function onConstruct()
    {
        $annotations = $this->getDI()->get('annotations')->get($this);
        foreach ($annotations->getPropertiesAnnotations() as $propertyName => $propertyAnnotation) {
            $this->setPropertyValue($propertyName, $propertyAnnotation);
        }
    }

    /**
     * @param string $propertyName
     * @param Collection $annotations
     */
    private function setPropertyValue($propertyName, Collection $annotations)
    {
        if ($annotations->has('Value')) {
            $this->$propertyName = $this->getDI()->get('config')->{$annotations->get('Value')->getArgument(0)};

        } else {
            $this->setPropertyService($propertyName, $annotations);
        }
    }

    /**
     * @param string $propertyName
     * @param Collection $annotations
     */
    private function setPropertyService($propertyName, Collection $annotations)
    {
        if ($annotations->has('Service')) {
            $this->$propertyName = $this->getDI()->get($annotations->get('Service')->getArgument(0));

        }
    }
}
