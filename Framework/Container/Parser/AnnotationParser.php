<?php

namespace mirolabs\phalcon\Framework\Container\Parser;

use Phalcon\Annotations\Adapter;
use Phalcon\Annotations\Collection;
use Phalcon\Exception;

class AnnotationParser
{
    /**
     * @var array
     */
    private $parameters;


    /**
     * @var Adapter
     */
    private $annotationAdapter;

    /**
     * @param array $parameters
     * @param Adapter $annotationAdapter
     */
    public function __construct(array $parameters, Adapter $annotationAdapter)
    {
        $this->parameters = $parameters;
        $this->annotationAdapter = $annotationAdapter;
    }

    /**
     * @param string $className
     * @return array
     */
    public function getProperties($className)
    {
        $result = [];
        $properties = $this->annotationAdapter->getProperties($className);
        if (is_array($properties)) {
            foreach ($properties as $name => $property) {
                $this->addPropertyDefinition($name, $property, $result);
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param Collection $annotations
     * @param array $result
     */
    private function addPropertyDefinition($name, Collection $annotations, &$result)
    {
        $this->addPropertyService($name, $annotations, $result);
        $this->addPropertyValue($name, $annotations, $result);
    }

    /**
     * @param string $name
     * @param Collection $annotations
     * @param array $result
     */
    private function addPropertyService($name, Collection $annotations, &$result)
    {
        if ($annotations->has('Service')) {
            $service = $annotations->get('Service');
            $result[] = [
                'name' => $name,
                'value' => ['type' => 'service', 'name' => $service->getArgument(0)]
            ];
        }
    }

    /**
     * @param string $name
     * @param Collection $annotations
     * @param array $result
     */
    private function addPropertyValue($name, Collection $annotations, &$result)
    {
        if ($annotations->has('Value')) {
            $value = $annotations->get('Value');
            $result[] = [
                'name' => $name,
                'value' => ['type' => 'parameter', 'value' => $this->getParameterValue($value->getArgument(0))]
            ];
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Phalcon\Exception
     */
    private function getParameterValue($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key]->getValue();
        }

        throw new Exception('Parameter ' . $key . ' is not exists');
    }
}
