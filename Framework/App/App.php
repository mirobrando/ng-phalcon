<?php

namespace mirolabs\phalcon\Framework\App;

interface App {
    function setModules($modules);
    function runException(\Exception $ex);
    function getDI();
    function execute();
    function getContainer();
    function getUri();
}
