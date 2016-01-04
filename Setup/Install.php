<?php

namespace mirolabs\phalcon\Setup;

class Install
{
    public static function createProject()
    {
        CreateFolders::execute();
        CreateController::execute();
    }
}
