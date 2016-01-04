<?php

namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Services\RegisterService;

interface Container {
    function registerServices(RegisterService $registerService);
}
