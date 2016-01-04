<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use Phalcon\Mvc\Url as UrlResolver;

class Url implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService) {
        $url = new UrlResolver();
        $url->setBaseUri('/');

        $registerService->getDependencyInjection()->set('url', $url);
    }
}
