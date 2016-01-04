<?php

namespace mirolabs\phalcon\Task;

use mirolabs\phalcon\Framework\Task;
use mirolabs\console\Output\Style;

class CommandListTask extends Task
{

    public function runAction($param)
    {
        $this->output()->writeln('');
        $this->output()->writelnStyle('Task List:', new Style('white', 'blue', 'bold'));
        $this->output()->writeln('');
        foreach ($param['tasks'] as $name => $param) {
            $this->output()->writeFormat($name, 'info_bold');
            $this->output()->write(' - ');
            $this->output()->writelnFormat($param['description'], 'comment');
        }
    }
}
