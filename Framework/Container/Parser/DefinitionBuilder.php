<?php

namespace mirolabs\phalcon\Framework\Container\Parser;


use mirolabs\phalcon\Framework\Container\Parser;
use mirolabs\phalcon\Framework\Tasks\FileBuilder;

class DefinitionBuilder
{
    /**
     * @var string
     */
    private $fileContainer;

    /**
     * @var \mirolabs\phalcon\Framework\Tasks\FileBuilder
     */
    private $fileBuilder;

    /**
     * @param string $fileContainer
     * @param FileBuilder $fileBuilder
     */
    public function __construct($fileContainer, FileBuilder $fileBuilder)
    {
        $this->fileContainer = $fileContainer;
        $this->fileBuilder = $fileBuilder;
    }

    /**
     * create file
     */
    public function createFile()
    {
        $this->fileBuilder->createFile($this->fileContainer, 0777, true);
    }

    /**
     *
     * write message to file
     * @param $message
     */
    public function write($message)
    {
        file_put_contents($this->fileContainer, $message, FILE_APPEND);
    }

    /**
     * write once line to file
     * @param $line
     */
    public function writeLine($line)
    {
        $this->write($line . "\n");
    }
}
