<?php

namespace mirolabs\phalcon\Framework\View;


use Phalcon\Mvc\View;

class ConsoleView extends View {


    /**
     * @param mixed $moduleName
     */
    public function setModuleName($moduleName)
    {
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return 'console';
    }

}