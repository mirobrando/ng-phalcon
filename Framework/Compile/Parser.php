<?php

namespace mirolabs\phalcon\Framework\Compile;

use Phalcon\Annotations\Adapter;
use mirolabs\phalcon\Framework\Compile\Plugin\Service;
use mirolabs\phalcon\Framework\Compile\Plugin\Listener;
use mirolabs\phalcon\Framework\Compile\Plugin\Route;
use mirolabs\phalcon\Framework\Compile\Plugin\Task;
use mirolabs\phalcon\Framework\Module;
use Phalcon\Config\Adapter\Yaml;

class Parser {

    /**
     * @var Adapter
     */
    private $annotationAdapter;

    /**
     * @var string
     */
    private $modules;

    /**
     * @var Yaml
     */
    private $config;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $projectPath;
    
    /**
     * @var Plugin[] 
     */
    private $plugins = [];




    /**
     * @param string $projectPath
     * @param array $modules
     * @param Adapter $annotationAdapter
     */
    public function __construct($projectPath, $modules, $annotationAdapter)
    {
        $this->modules = $modules;
        $this->projectPath = $projectPath;
        $this->config = new Yaml($projectPath . 'config/config.yml');
        $this->cacheDir = $projectPath . Module::COMMON_CACHE;
        $this->annotationAdapter = $annotationAdapter;
        $this->addPlugin(new Service());
        $this->addPlugin(new Listener());
        $this->addPlugin(new Route());
        $this->addPlugin(new Task());
        $this->registerPlugins();
    }
    
    private function registerPlugins() {
        foreach ($this->modules as $module) {
            $moduleClass = new $module['className'];
            $this->addPlugins($moduleClass->getAnnotationPlugins());
        }
    }

    public function execute() {
        foreach ($this->getModulePaths() as $moduleName => $module) {
            $this->parseFolder($moduleName, $module['folder'], $module['namespace']);
        }
        $this->craeteCache();
    }

    public function addPlugins($plugins) {
        if (is_array($plugins)) {
            foreach($plugins as $plugin) {
                $this->addPlugin($plugin);
            }
        }
    }
    
    public function addPlugin(Plugin $plugin) {
        $plugin->setConfig($this->config);
        $this->plugins[] = $plugin;
    }

    private function getModulePaths() {
        $modulesPath = [];
        foreach ($this->modules as $moduleName => $module) {
            $matchesPath = null;
            $matchesNs = null;
            preg_match('/([A-Za-z\/-]+)Module\.php/', $module['path'], $matchesPath);
            preg_match('/([A-Za-z\\\]+)\\\Module/', $module['className'], $matchesNs);
            
            $modulesPath[$moduleName] = [
                'folder' => $this->projectPath . $matchesPath[1],
                'namespace' => $matchesNs[1]
            ];
        }
        
        return $modulesPath;
    }

    
    /**
     * @param string $moduleName
     * @param string $folder
     * @param string $namespace
     */
    private function parseFolder($moduleName, $folder, $namespace) {
        if ($dir = opendir($folder)) {
            while (($file = readdir($dir)) !== false) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }

                if (is_dir($folder. '/' .$file)) {
                    $this->parseFolder($moduleName, $folder. '/' . $file, $namespace . '\\' . $file);
                }

                $this->parseFile($moduleName, $folder. '/' .$file, $namespace . '\\' . $file);
            }
            closedir($dir);
        }
    }
    
    /**
     * @param string $moduleName
     * @param string $filePath
     * @param string $namespace
     */
    private function parseFile($moduleName, $filePath, $namespace) {
        if (file_exists($filePath)) {
            if (strpos($filePath, '.php') !== strlen($filePath) - 4) {
                return [];
            } 
            $className = substr($namespace, 0, strlen($namespace) -4);
            if (class_exists($className)) {
                $this->callParseFile($className, $moduleName);
            }
        }
    }
    
    private function callParseFile($className, $module) {
        foreach ($this->plugins as $plugin) {
            $plugin->parseFile($this->annotationAdapter, $className, $module);
        }
    }
    
    private function craeteCache() {
        foreach ($this->plugins as $plugin) {
            $plugin->createCache($this->cacheDir);
        }
    }

    
}
