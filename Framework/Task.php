<?php

namespace mirolabs\phalcon\Framework;


use mirolabs\console\Output\StandardOutput;
use mirolabs\console\Reader;
use Phalcon\CLI\Task as TaskCli;

class Task extends TaskCli
{
    /**
     * @var Reader
     */
    private $input;

    /**
     * @var StandardOutput
     */
    private $output;

    /**
     * @return Reader
     */
    protected function input()
    {
        if (is_null($this->input)) {
            $this->input = new Reader($this->output());
        }

        return $this->input;
    }

    /**
     * @return StandardOutput
     */
    protected function output()
    {
        if (is_null($this->output)) {
            $this->output = new StandardOutput();
        }

        return $this->output;
    }
}

