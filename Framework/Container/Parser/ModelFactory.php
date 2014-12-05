<?php

namespace mirolabs\phalcon\Framework\Container\Parser;

use mirolabs\phalcon\Framework\Container\Parser\Model\Factory;
use mirolabs\phalcon\Framework\Container\Parser\Model\Listener;
use mirolabs\phalcon\Framework\Container\Parser\Model\Service;

class ModelFactory
{
    /**
     * @param $serviceName
     * @param $serviceParameters
     * @param AttributeParser $attributeParser
     * @param AnnotationParser $annotation
     * @return Output
     */
    public function getServiceModel($serviceName, $serviceParameters, AttributeParser $attributeParser, $annotation)
    {
        return $this->getModelListener($serviceName, $serviceParameters, $attributeParser, $annotation);
    }

    /**
     * @param $serviceName
     * @param $serviceParameters
     * @param AttributeParser $attributeParser
     * @param AnnotationParser $annotation
     * @return Output
     */
    private function getModelListener($serviceName, $serviceParameters, AttributeParser $attributeParser, $annotation)
    {
        if ((array_key_exists(Listener::ATTRIBUTE_EVENTS, $serviceParameters) ||
            (array_key_exists(Listener::ATTRIBUTE_EVENT_NAME, $serviceParameters) &&
                array_key_exists(Listener::ATTRIBUTE_EVENT_METHOD, $serviceParameters)))
        ) {
            return new Listener($attributeParser, $annotation, $serviceName, $serviceParameters);
        }

        return $this->getModelFactory($serviceName, $serviceParameters, $attributeParser, $annotation);
    }

    /**
     * @param $serviceName
     * @param $serviceParameters
     * @param AttributeParser $attributeParser
     * @param AnnotationParser $annotation
     * @return Output
     */
    private function getModelFactory($serviceName, $serviceParameters, AttributeParser $attributeParser, $annotation)
    {
        if (array_key_exists(Factory::ATTRIBUTE_FACTORY_SERVICE, $serviceParameters) &&
            array_key_exists(Factory::ATTRIBUTE_FACTORY_METHOD, $serviceParameters)
        ) {
            return new Factory($attributeParser, $annotation, $serviceName, $serviceParameters);
        }

        return $this->getModelService($serviceName, $annotation, $serviceParameters, $attributeParser);
    }

    /**
     * @param $serviceName
     * @param $serviceParameters
     * @param AttributeParser $attributeParser
     * @param AnnotationParser $annotation
     * @return Service
     */
    private function getModelService($serviceName, $serviceParameters, AttributeParser $attributeParser, $annotation)
    {
        return new Service($attributeParser, $annotation, $serviceName, $serviceParameters);
    }
}
