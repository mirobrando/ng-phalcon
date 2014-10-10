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
        $result = true;
        try {
            foreach($this->modulesPath as $modulePath) {
                $serviceFile = $modulePath . '/' . Module::CONFIG;
                $time = filemtime($serviceFile);
                if (array_key_exists($serviceFile, $data)) {
                    $result &= $time == $data[$serviceFile];
                } else {
                    $result = false;
                }
                $data[$serviceFile] = $time;
            }

        } catch (\Exception $e) {
            $result = false;
        }

        if (!$result) {
            $this->saveCache($data);
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
            $data = file_get_contents($this->cacheDir . '/' . self::CACHE_FILE);
            return json_decode($data);
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
            file_put_contents($this->cacheDir . '/' . self::CACHE_FILE, $data);
        } catch (\Exception $e) {
        }
    }


}