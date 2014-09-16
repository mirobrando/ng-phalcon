<?php

namespace mirolabs\phalcon\Framework;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Translate\Adapter\NativeArray;


class Translation
{

    /**
     * @var \Phalcon\Paginator\Adapter\NativeArray
     */
    private $translate;

    public function __construct(Dispatcher $dispatcher, array $modules)
    {
        $lang = $dispatcher->getParam('language');
        if (is_null($lang)) {
            $lang = 'pl';
        }

        require $modules[$dispatcher->getModuleName()] . 'messages/' . $lang . '.php';

        $this->translate =  new NativeArray([
            'content' => $messages
        ]);
    }

    public function __get($name)
    {
        return $this->translate->_($name);
    }


} 