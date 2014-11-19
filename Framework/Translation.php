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

    /**
     * @var string
     */
    private $lang;

    public function __construct(Dispatcher $dispatcher, array $modules, $defaultLanguage = 'en')
    {
        $this->lang = $dispatcher->getParam('language');
        if (is_null($this->lang)) {
            $this->lang = $defaultLanguage;
        }

        $config = $dispatcher->getDI()->get('config');
        $translations = $this->getMessages($config->projectPath . 'common/');
        $translations = array_merge(
            $translations,
            $this->getMessages($modules[$dispatcher->getModuleName()])
        );

        $this->translate =  new NativeArray([
            'content' => $translations
        ]);
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function __get($name)
    {
        return $this->translate->_($name);
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param $dir
     *
     * @return array
     */
    protected function getMessages($dir)
    {
        $file = sprintf("%smessages/%s.php", $dir, $this->lang);
        if (file_exists($file)) {
            require $file;
            return $messages;
        }

        return [];
    }
}
