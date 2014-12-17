<?php

namespace mirolabs\phalcon\Framework;

use mirolabs\phalcon\Framework\Type\RegisterService;

interface Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService);
}
