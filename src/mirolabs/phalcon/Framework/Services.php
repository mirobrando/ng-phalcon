<?php

namespace mirolabs\phalcon\Framework;

interface Services
{
    /**
     * create container dependency injector
     * @return \Phalcon\DI\FactoryDefault
     */
    public function createContainer();

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setConfig($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setDb($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setRouter($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setUrl($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setSession($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function setTranslation($di);

    /**
     * @param \Phalcon\DI\FactoryDefault $di
     * @return void
     */
    public function registerOtherServices($di);

}