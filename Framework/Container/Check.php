<?php

namespace mirolabs\phalcon\Framework\Container;


use mirolabs\phalcon\Framework\Module;

class Check
{
    const CACHE_FILE = '.logfile';

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var string
     */
    private $modulesPath;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $projectPath
     * @param string $modulesPath
     * @param string $cacheDir
     */
    public function __construct($projectPath, $modulesPath, $cacheDir)
    {
        $this->projectPath = $projectPath;
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
        foreach ($this->getServicesPath() as $path) {
            if ($this->isChangeConfigurationModule($path, $data)) {
                $result = true;
                //$this->saveCache();
                break;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getServicesPath()
    {
        $result[] = $this->projectPath . '/' . Module::CONFIG;
        foreach ($this->modulesPath as $modulePath) {
            $result[] = $modulePath . '/' . Module::SERVICE;
        }

        return $result;
    }


    /**
     * @param string $servicesFile
     * @param array $data
     * @return bool
     */
    private function isChangeConfigurationModule($servicesFile, array $data)
    {
        $result = true;
        if (array_key_exists($servicesFile, $data)) {
            $result = (filemtime($servicesFile) != $data[$servicesFile]);
        }

        if (!$result) {
            $result = $this->isChangeClassesModules($servicesFile, $data);
        }

        return $result;
    }


    /**
     * @param string $servicesFile
     * @param array $data
     * @return bool
     */
    private function isChangeClassesModules($servicesFile, $data)
    {

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
