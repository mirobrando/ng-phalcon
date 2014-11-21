<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Output;

class Factory extends Service implements Output
{
    const ATTRIBUTE_FACTORY_SERVICE = 'factory_service';
    const ATTRIBUTE_FACTORY_METHOD = 'factory_method';

    /**
     * @param DefinitionBuilder $definitionBuilder
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder)
    {
        $definitionBuilder->writeLine(sprintf("\t\t\$di->set('%s', function() use (\$di) {", $this->serviceName));
        $this->writeFactoryArguments($definitionBuilder);
        $definitionBuilder->writeLine("\t\t});");

    }

    /**
     * @param DefinitionBuilder $definitionBuilder
     */
    private function writeFactoryArguments(DefinitionBuilder $definitionBuilder)
    {
        $argumentsFactory = [];
        foreach ($this->getArguments() as $argument) {
            if ($argument['type'] == 'service') {
                $argumentsFactory[] = sprintf("\$di->get('%s')", $argument['name']);
            } else {
                $argumentsFactory[] = sprintf("'%s'", $argument['value']);
            }
        }
        $definitionBuilder->writeLine(sprintf(
            "\t\t\treturn \$di->get('%s')->%s(%s);",
            $this->getFactoryClass(),
            $this->getFactoryMethod(),
            implode(", ", $argumentsFactory)
        ));
    }

    /**
     * @return string
     */
    protected function getFactoryClass()
    {
        return $this->values[self::ATTRIBUTE_FACTORY_SERVICE];
    }

    /**
     * @return string
     */
    protected function getFactoryMethod()
    {
        return $this->values[self::ATTRIBUTE_FACTORY_METHOD];
    }
}
