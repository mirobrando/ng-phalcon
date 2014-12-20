<?php

namespace mirolabs\phalcon\Framework\Container;


use mirolabs\phalcon\Framework\Application;
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
    private $environment;

    /**
     * @param string $projectPath
     * @param string $modulesPath
     * @param string $environment
     */
    public function __construct($projectPath, $modulesPath, $environment)
    {
        $this->projectPath = $projectPath;
        $this->modulesPath = $modulesPath;
        $this->environment = $environment;
    }

    /**
     * @return bool
     */
    public function isChangeConfiguration()
    {
        $result = true;
        if (file_exists($this->getFileLog())) {
            $result = $this->isChangedFiles();
        }

        if ($result) {
            $this->saveCache($this->createNewCache());
        }

        return $result;
    }


    /**
     * @return bool
     */
    private function isChangedFiles()
    {
        $result = false;
        if ($this->environment != Application::ENVIRONMENT_PROD) {
            $data = $this->loadCache();
            $result =   $this->isChangedFile($this->projectPath . Module::CONFIG, $data) ||
                        $this->isChangedModule($this->modulesPath, $data);
        }

        return $result;
    }

    /**
     * @param array $modules
     * @param array $data
     * @return bool
     */
    private function isChangedModule(array $modules, array $data)
    {
        if (count($modules) == 0) {
            return false;
        }

        $modulePath = array_pop($modules);

        return
            $this->isChangedFile($modulePath . Module::SERVICE, $data) ||
            $this->isChangedFile($modulePath . 'services/', $data) ||
            $this->isChangedModule($modules, $data);
    }

    private function isChangedFilesInFolder($folder, array $data)
    {
        $result = false;
        if ($dir = opendir($folder)) {
            while (($file = readdir($dir)) !== false) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $result |= $this->isChangedFile($folder . $file, $data);
            }
            closedir($dir);
            $result = !$result;
        }
        return !$result;
    }

    /**
     * @param string $filePath
     * @param array $data
     * @return bool
     */
    private function isChangedFile($filePath, array $data)
    {
        if (file_exists($filePath)) {
            if (is_dir($filePath)) {
                return $this->isChangedFilesInFolder($filePath, $data);
            }

            return !(array_key_exists($filePath, $data) && filemtime($filePath) == $data[$filePath]);
        }

        return false;
    }

    /**
     * @return string
     */
    private function getFileLog()
    {
        return $this->projectPath . Module::COMMON_CACHE . '/' . self::CACHE_FILE;
    }

    /**
     * @return array
     */
    private function loadCache()
    {
        $result = [];
        try {
            if (file_exists($this->getFileLog())) {
                $result = unserialize(file_get_contents($this->getFileLog()));
            }
        } catch (\Exception $e) {
        }
        return $result;
    }

    /**
     * @param array $data
     */
    private function saveCache($data)
    {
        try {
            @file_put_contents($this->getFileLog(), serialize($data));
            @chmod($this->getFileLog(), 0777);
        } catch (\Exception $e) {
        }
    }

    /**
     * @return array
     */
    private function createNewCache()
    {
        $data = $this->createCacheForModule($this->modulesPath);
        $data[$this->projectPath . Module::CONFIG] = filemtime($this->projectPath . Module::CONFIG);

        return $data;
    }

    private function createCacheForModule($modules)
    {
        if (count($modules) == 0) {
            return [];
        }

        $modulePath = array_pop($modules);

        return array_merge(
            $this->createCacheForFile($modulePath . Module::SERVICE),
            $this->createCacheForFile($modulePath . 'services/'),
            $this->createCacheForModule($modules)
        );
    }


    /**
     * @param $folder
     * @return array
     */
    private function createCacheForFolder($folder)
    {
        $result = [];
        if ($dir = opendir($folder)) {
            while (($file = readdir($dir)) !== false) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $result = array_merge($result, $this->createCacheForFile($folder . $file));
            }
            closedir($dir);
        }
        return $result;
    }

    /**
     * @param $filePath
     * @return array
     */
    private function createCacheForFile($filePath)
    {
        if (file_exists($filePath)) {
            if (is_dir($filePath)) {
                return $this->createCacheForFolder($filePath);
            }

            return [$filePath => filemtime($filePath)];
        }

        return [];
    }
}
