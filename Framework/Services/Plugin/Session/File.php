<?php

namespace mirolabs\phalcon\Framework\Services\Plugin\Session;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use Phalcon\Session\Adapter\Files as SessionAdapter;

class File implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $session = new SessionAdapter();
        $session->start();
        $registerService->getDependencyInjection()->set('session', $session);
    }
}
