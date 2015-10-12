<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\Services\RegisterService;

interface Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService);
}
