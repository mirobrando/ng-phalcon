<?php

namespace mirolabs\phalcon\Framework\Services\Plugin;

use mirolabs\phalcon\Framework\Service;
use mirolabs\phalcon\Framework\Services\RegisterService;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

class Database implements Service
{
    /**
     * @param RegisterService $registerService
     */
    public function register(RegisterService $registerService)
    {
        if ($registerService->getDependencyInjection()->has('db')) {
            return;
        }
        $config = $registerService->getDependencyInjection()->get('config');
        $registerService->getDependencyInjection()->set('db', function () use ($config) {
            return new DbAdapter([
                'host' => $config->database->host,
                'port' => $config->database->port,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname' => $config->database->dbname,
                'charset' => $config->database->charset

            ]);
        });
    }
}
