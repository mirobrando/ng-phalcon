<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin;

use Phalcon\Config;
use mirolabs\collection\ArrayList;
use mirolabs\phalcon\Framework\Compile\Plugin\Model\Listener as ModelListener;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;

class Listener implements \mirolabs\phalcon\Framework\Compile\Plugin 
{
    const EVENT_ANNOTATION = 'Listener';
    const CACHE_FILE = '/listeners.php';

    /**
     * @var Config 
     */
    private $config;
    
    /**
     * @var ArrayList
     */
    private $listeners = [];
    
    public function __construct() {
        $this->listeners = new ArrayList('\mirolabs\phalcon\Framework\Compile\Plugin\Model\Listener');
    }

    public function getConfig() {
        return $this->config;
    }

    public function setConfig(Config $config) {
        $this->config = $config;
    }
    
    public function createCache($cacheDir) {
        $file = "<?php\n\n";
        $file .= "\tfunction _loadListeners(\$di) {\n";
        $file .= implode("\n", $this->buildListeners());
        $file .= "\n\t}\n";
        file_put_contents($cacheDir . self::CACHE_FILE, $file);
    }

    public function parseFile(\Phalcon\Annotations\Adapter $adapter, $className, $module) {
        $parser = new AnnotationParser($adapter->get($className));
        if ($parser->isExistsAnnotationClass(Service::CLASS_ANNOTATION)) {
            $this->listeners->add(new ModelListener($parser, $className));
        }    
    }
    
    private function buildListeners() {
        $result = [];
        $this->listeners
                ->map(function (ModelListener $model) { return $model->getListeners(); })
                ->filter(function ($data) { return count($data) > 0;})
                ->each(function ($data) use (&$result) { $result = array_merge($result, $data); });
        return $result;        
    }
    
}
