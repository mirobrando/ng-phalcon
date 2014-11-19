<?php

namespace mirolabs\phalcon\Framework\Container\Parser;


interface Output
{
    /**
     * @param DefinitionBuilder $definitionBuilder
     * @return void
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder);
} 