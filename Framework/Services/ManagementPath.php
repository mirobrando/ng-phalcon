<?php

namespace mirolabs\phalcon\Framework\Services;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Type\RegisterService;

class ManagementPath implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        $registerService->getDependencyInjection()->set('managementPath', [
            'className' => 'mirolabs\phalcon\Framework\View\ManagementPath',
            'arguments' => [
                ['type' => 'parameter', 'value' => $registerService->getProjectPath() . 'common/views/'],
                ['type' => 'parameter', 'value' => $registerService->getEnvironment()]
            ]
        ]);
    }
}
