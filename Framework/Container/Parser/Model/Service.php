<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\AnnotationParser;
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
     * @var AnnotationParser
     */
    protected $annotationParser;

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
     * @param AnnotationParser $annotationParser
     * @param string $serviceName
     * @param array $values
     */
    public function __construct(
        AttributeParser $attributeParser,
        AnnotationParser $annotationParser,
        $serviceName,
        array $values
    ) {
        $this->attributeParser = $attributeParser;
        $this->annotationParser = $annotationParser;
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
        $definitionBuilder->writeLine("\t\t\t],");
        $definitionBuilder->writeLine("\t\t\t'properties' => [");
        $this->writeServiceProperties($definitionBuilder);
        $definitionBuilder->writeLine("\t\t\t]");
        $definitionBuilder->writeLine("\t\t], true);");
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

    private function writeServiceProperties(DefinitionBuilder $definitionBuilder)
    {
        $properties = [];
        foreach ($this->annotationParser->getProperties($this->getClassName()) as $property) {
            $definition = sprintf("\t\t\t\t\t'name' => '%s',\n", $property['name']);
            if ($property['value']['type'] == 'service') {
                $pattern = "\t\t\t\t\t'value' => ['type' => 'service', 'name' => '%s']";
                $definition .= sprintf($pattern, $property['value']['name']);
            } else {
                $pattern = "\t\t\t\t\t'value' => ['type' => 'parameter', 'value' => '%s']";
                $definition .= sprintf($pattern, $property['value']['value']);
            }

            $properties[] = sprintf("\t\t\t\t[\n%s\n\t\t\t\t]", $definition);
        }
        $definitionBuilder->writeLine(implode(",\n", $properties));
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return $this->attributeParser->getClassValue($this->values[self::ATTRIBUTE_CLASS_NAME]);
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
