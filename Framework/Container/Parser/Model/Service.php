<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\AttributeParser;
use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Output;

class Service implements Output
{

    const ATTRIBUTE_CLASS_NAME = 'class';
    const ATTRIBUTE_ARGUMENTS = 'arguments';

    /**
     * @var AttributeParser
     */
    protected $attributeParser;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param AttributeParser $attributeParser
     * @param $serviceName
     * @param array $values
     */
    public function __construct(AttributeParser $attributeParser, $serviceName, array $values)
    {
        $this->attributeParser = $attributeParser;
        $this->serviceName = $serviceName;
        $this->values = $values;
    }

    /**
     * @param DefinitionBuilder $definitionBuilder
     * @return void
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder)
    {
        $definitionBuilder->writeLine(sprintf("\t\t\$di->set('%s', [", $this->serviceName));
        $definitionBuilder->writeLine(sprintf("\t\t\t'className' => '%s',", $this->getClassName()));
        $definitionBuilder->writeLine("\t\t\t'arguments' => [");
        $this->writeServiceArguments($definitionBuilder);
        $definitionBuilder->writeLine("\t\t\t]");
        $definitionBuilder->writeLine("\t\t]);");
    }

    private function writeServiceArguments(DefinitionBuilder $definitionBuilder)
    {
        $argumentsService = [];
        foreach ($this->getArguments() as $argument) {
            $key = 'value';
            if ($argument['type'] == 'service') {
                $key = 'name';
            }
            $argumentsService[] = sprintf(
                "\t\t\t\t['type' => '%s', '%s' => '%s']",
                $argument['type'],
                $key,
                $argument[$key]
            );
        }
        $definitionBuilder->writeLine(implode(",\n", $argumentsService));
    }


    /**
     * @return string
     */
    protected function getClassName()
    {
        return $this->attributeParser->getClassValue(self::ATTRIBUTE_CLASS_NAME);
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        $result = [];
        if (array_key_exists(self::ATTRIBUTE_ARGUMENTS, $this->values) &&
            is_array($this->values[self::ATTRIBUTE_ARGUMENTS])
        ) {
            foreach ($this->values[self::ATTRIBUTE_ARGUMENTS] as $argument) {
                $result[] = $this->getArgument($argument);
            }
        }

        return $result;
    }

    /**
     * @param $argument
     * @return array
     */
    protected function getArgument($argument)
    {
        return $this->attributeParser->getArgumentValue($argument);
    }
}
