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

        $config = $dispatcher->getDI()->get('config')->data;
        $translations = $this->getMessages($config['projectPath'] . 'common/', $lang);
        $translations = array_merge(
            $translations,
            $this->getMessages($modules[$dispatcher->getModuleName()], $lang)
        );

        $this->translate =  new NativeArray([
            'content' => $translations
        ]);
    }

    public function __get($name)
    {
        return $this->translate->_($name);
    }

    /**
     * @param $dir
     * @param $lang
     * @return array
     */
    protected function getMessages($dir, $lang)
    {
        $file = $dir . 'messages/' . $lang . '.php';;
        if (file_exists($file)) {
            require $file;
            return $messages;
        }

        return [];
    }
} 