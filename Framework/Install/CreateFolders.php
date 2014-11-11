<?php

namespace mirolabs\phalcon\Framework\Install;


class CreateFolders
{
    public static function execute()
    {
        $folder = getcwd();

        mkdir($folder . '/common/cache', 0777);
        mkdir($folder . '/common/resources/css', 0755);
        mkdir($folder . '/common/resources/img', 0755);

        mkdir($folder . '/modules', 0755);

        mkdir($folder . '/public/css', 0755);
        mkdir($folder . '/public/img', 0755);
        mkdir($folder . '/public/js', 0755);
        mkdir($folder . '/public/views', 0755);
    }



} 