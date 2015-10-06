<?php

namespace mirolabs\phalcon\Framework\Container;

class Service extends \Phalcon\DI\Service
{

    public function resolve($parameters = null, \Phalcon\DiInterface $dependencyInjector = null) 
    {
        if (is_null($this->_sharedInstance)) {
            try {
                $className = $this->_definition['className'];
                $service = new $className();
                $this->_sharedInstance = new ServiceDecorator($service);
                $this->_resolved = true;

                foreach ($this->_definition['properties'] as $property) {
                    $this->_sharedInstance->setProperty($property['name'], 
                            $this->getPropertValue($property['value'], $dependencyInjector));
                }
            } catch (\Exception $ex) {
                $this->_resolved = false;
                throw $ex;
            }
        }
        return $this->_sharedInstance;
    }
    
    private function getPropertValue($definition, $dependencyInjector)
    {
        if ($definition['type'] == 'service') {
            return $dependencyInjector->get($definition['name']);
        }
        return $definition['value'];
    }
}
