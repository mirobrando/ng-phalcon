<?php

namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Type\RegisterService;
use Phalcon\DI;
use Phalcon\Events\Manager as EventsManager;

class Listener implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        $eventsManager = new EventsManager();
        $registerService->getDependencyInjection()->set('listener', $eventsManager);
        $registerService->getDependencyInjection()->get('dispatcher')->setEventsManager($eventsManager);
    }
}
