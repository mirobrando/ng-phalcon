<?php

namespace mirolabs\phalcon\Framework\Tasks;


use mirolabs\phalcon\Framework\Task;

class ProjectTask extends Task
{


    public function listAction($param)
    {
        foreach($param['tasks'] as $name => $param) {
            $this->output()->writeFormat($name, 'info_bold');
            $this->output()->write(' - ');
            $this->output()->writelnFormat($param['description'], 'comment');
        }

    }


    public function createModuleAction($params)
    {
        var_dump($params);

    }


} 