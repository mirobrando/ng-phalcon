<?php // @codeCoverageIgnoreStart

namespace mirolabs\phalcon\Framework\Container\Parser;

/**
 * Interface Output
 * @package mirolabs\phalcon\Framework\Container\Parser
 */
interface Output
{
    /**
     * @param DefinitionBuilder $definitionBuilder
     * @return void
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder);
}
