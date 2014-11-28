<?php

namespace mirolabs\phalcon\Framework\Tasks;


use Phalcon\Exception;

class FileBuilder
{
    public function createFolder($folder, $mode = 0777)
    {
        if (!is_dir($folder)) {
            if (file_exists($folder)) {
                throw new Exception('given path is file');
            }
            mkdir($folder, $mode, true);
        }

        return $this;
    }


    public function createFile($path, $mode = 0777, $force = false)
    {
        if (!$force && file_exists($path)) {
            throw new Exception('file is exist');
        }

        file_put_contents($path, '');
        @chmod($path, $mode);

        return $this;
    }
}
