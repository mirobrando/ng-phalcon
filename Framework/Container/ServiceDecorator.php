<?php

namespace mirolabs\phalcon\Framework\Container;

class ServiceDecorator 
{
    private $serviceInstance;

    /**
     * @var \ReflectionObject
     */
    private $reflection;
    
    public function __construct($serviceInstance)
    {
        $this->serviceInstance = $serviceInstance;
        $this->reflection = new \ReflectionObject($this->serviceInstance);
    }
    
    public function setProperty($propertyName, $value)
    {
        $property = $this->reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->serviceInstance, $value);
    }
    
    public function __call($name, $arguments) 
    {
        return call_user_func_array([$this->serviceInstance,$name], $arguments);
    }
}
