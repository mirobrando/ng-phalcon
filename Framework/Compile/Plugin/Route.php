<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin;

use mirolabs\collection\ArrayList;
use mirolabs\phalcon\Framework\Compile\Plugin\Model\Route as ModelRoute;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;


class Route implements \mirolabs\phalcon\Framework\Compile\Plugin {
    
    const CONTROLER_ANNOTATION = 'Controller';
    const ROUTE_ANNOTATION = 'Route';
    const CACHE_FILE = '/routes.php';

    /**
     * @var \Phalcon\Config 
     */
    private $config;
    
    /**
     * @var ArrayList
     */
    private $routers = [];
    
    public function __construct() {
        $this->routers = new ArrayList('\mirolabs\phalcon\Framework\Compile\Plugin\Model\Route');
    }

    public function getConfig() {
        return $this->config;
    }

    public function setConfig(\Phalcon\Config $config) {
        $this->config = $config;
    }
    
    public function parseFile(\Phalcon\Annotations\Adapter $adapter, $className, $module) {
        $parser = new AnnotationParser($adapter->get($className));
        if ($parser->isExistsAnnotationClass(self::CONTROLER_ANNOTATION)) {
            $this->routers->add(new ModelRoute($parser, $className, $module));
        } 
    }

    public function createCache($cacheDir) {
        $file = "<?php\n\n";
        $file .= "\tfunction _loadRoutes(\$router) {\n";
        $file .= $this->buildRoutes();
        $file .= "\n\t}\n";
        file_put_contents($cacheDir . self::CACHE_FILE, $file);
    }
    
    public function buildRoutes() {
        $result = [];
        foreach($this->routers->map(function(ModelRoute $m) { return $m->getRoutes();}) as $data) {
            $result[] = implode("\n", $data);
        }   
        return implode("\n", $result);
    }

}
