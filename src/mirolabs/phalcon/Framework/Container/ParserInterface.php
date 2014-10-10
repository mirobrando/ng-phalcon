<?php

namespace mirolabs\phalcon\Framework\Container;


interface ParserInterface
{

    public function __construct(Output $output);

    /**
     * @return void
     */
    public function writeDefinition();
} 