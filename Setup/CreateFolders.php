<?php

namespace mirolabs\phalcon\Setup;


class CreateFolders
{
    public static function execute()
    {
        $folder = getcwd();
        mkdir($folder . '/common/cache', 0777, true);
        mkdir($folder . '/common/resources/css', 0755, true);
        mkdir($folder . '/common/resources/img', 0755, true);

        mkdir($folder . '/modules', 0755, true);

        mkdir($folder . '/public/css', 0755, true);
        mkdir($folder . '/public/img', 0755, true);
        mkdir($folder . '/public/js', 0755, true);
        mkdir($folder . '/public/views', 0755, true);
    }
}
