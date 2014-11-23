<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Output;

class Parameter implements Output
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param DefinitionBuilder $definitionBuilder
     * @return void
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder)
    {
        $definitionBuilder->writeLine(sprintf(
            "\t\t\$config->set('%s', '%s');",
            $this->name,
            json_encode($this->value)
        ));
    }
}
