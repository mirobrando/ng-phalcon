<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;

class Translation implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        if ($registerService->getDependencyInjection()->has('translation')) {
            return;
        }
        $lang = $registerService->getDependencyInjection()->get('config')->get('default.lang');
        if (empty($lang)) {
            $lang = 'en';
        }
        $registerService->getDependencyInjection()->set('translation', [
            'className' => 'mirolabs\phalcon\Framework\Translation',
            'arguments' => [
                ['type' => 'service', 'name' => 'dispatcher'],
                ['type' => 'parameter', 'value' => $registerService->getModulesPath()],
                ['type' => 'parameter', 'value' => $lang]
            ]
        ]);
    }
}
