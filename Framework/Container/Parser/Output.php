<?php

namespace mirolabs\phalcon\Framework\Container\Parser;

/**
 * Interface Output
 * @package mirolabs\phalcon\Framework\Container\Parser
 * @codeCoverageIgnore
 */
interface Output
{
    /**
     * @param DefinitionBuilder $definitionBuilder
     * @return void
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder);
}
