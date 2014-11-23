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
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setListenerManager($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setDb($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setRouter($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setUrl($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setSession($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function setTranslation($dependencyInjection);

    /**
     * @param \Phalcon\DI\FactoryDefault $dependencyInjection
     * @return void
     */
    public function registerUserServices($dependencyInjection);
}
