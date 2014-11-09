<?php

namespace mirolabs\phalcon\Framework\Install;


class CreateFolders
{
    public static function execute()
    {
        $folder = getcwd();

        mkdir($folder . '/common/cache', 777);
        mkdir($folder . '/common/resources', 755);
        mkdir($folder . '/common/resources/css', 755);
        mkdir($folder . '/common/resources/img', 755);
        mkdir($folder . '/common/resources/js', 755);
        mkdir($folder . '/common/resources/ng-views', 755);

        mkdir($folder . '/modules', 755);

        mkdir($folder . '/public/css', 755);
        mkdir($folder . '/public/img', 755);
        mkdir($folder . '/public/js', 755);
        mkdir($folder . '/public/views', 755);
    }



} 