<?php

namespace mirolabs\phalcon\Framework\Tasks;

use mirolabs\phalcon\Framework\Task;

class CommandListTask extends Task
{

    public function runAction($param)
    {
        foreach($param['tasks'] as $name => $param) {
            $this->output()->writeFormat($name, 'info_bold');
            $this->output()->write(' - ');
            $this->output()->writelnFormat($param['description'], 'comment');
        }
    }

} 