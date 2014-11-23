<?php

namespace mirolabs\phalcon\Framework\Container;


use mirolabs\phalcon\Framework\Module;

class Check
{
    const CACHE_FILE = '.logfile';


    /**
     * @var string
     */
    private $modulesPath;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $modulesPath
     * @param string $cacheDir
     */
    public function __construct($modulesPath, $cacheDir)
    {
        $this->modulesPath = $modulesPath;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return bool
     */
    public function isChangeConfiguration()
    {
        $data = $this->loadCache();
        $result = false;
        try {
            foreach ($this->modulesPath as $modulePath) {
                $serviceFile = $modulePath . '/' . Module::SERVICE;
                $result |= $this->checkFile($serviceFile, $data);
            }

        } catch (\Exception $e) {
            $result = true;
        }

        if ($result) {
            $this->saveCache($data);
        }
        return $result;
    }


    /**
     * @param string $file
     * @param array $data
     * @return bool
     */
    private function checkFile($file, array &$data)
    {
        $result = true;
        if (array_key_exists($file, $data)) {
            $result = filemtime($file) != $data[$file];
            $data[$file] = filemtime($file);
        }

        return $result;
    }





    /**
     * @return bool
     */
    public function isCacheExist()
    {
        return file_exists($this->cacheDir . '/' . self::CACHE_FILE);
    }


    /**
     * @return array
     */
    private function loadCache()
    {
        try {
            if ($this->isCacheExist()) {
                $data = file_get_contents($this->cacheDir . '/' . self::CACHE_FILE);
                return unserialize($data);
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $data
     */
    private function saveCache($data)
    {
        try {
            @file_put_contents($this->cacheDir . '/' . self::CACHE_FILE, serialize($data));
            @chmod($this->cacheDir . '/' . self::CACHE_FILE, 0777);
        } catch (\Exception $e) {
        }
    }
}
