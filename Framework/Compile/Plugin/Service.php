<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin;

use Phalcon\Annotations\Adapter as Annotations;
use Phalcon\Config;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Model\Service as ModelService;
use mirolabs\collection\ArrayList;

class Service implements \mirolabs\phalcon\Framework\Compile\Plugin {

    const CLASS_ANNOTATION = 'Service';
    const PROPERTY_ANNOTATION = 'Inject';
    const PROPERTY_ANNOTATION_VALUE = 'Value';
    const CACHE_FILE = '/services.php';


    /**
     * @var Config 
     */
    private $config;
    
    /**
     * @var ArrayList
     */
    private $services;

    use \mirolabs\phalcon\Framework\Compile\Plugin\Config;
    
    public function __construct()
    {
        $this->services = new ArrayList('\mirolabs\phalcon\Framework\Compile\Plugin\Model\Service');
    }

    public function getConfig() 
    {
        return $this->config;
    }

    public function setConfig(Config $config) 
    {
        $this->config = $config;
    }

    public function parseFile(Annotations $adapter, $className, $module)
    {
        $parser = new AnnotationParser($adapter->get($className));
        if ($parser->isExistsAnnotationClass(self::CLASS_ANNOTATION)) {
            $this->services->add(new ModelService($parser, $className, 
                    function($className) {return $this->getServiceName($className);},
                    function($value) {return $this->getValue($value);}));
        }
    }
    
    public function createCache($cacheDir) 
    {
        $file = "<?php\n\n";
        $file .= "\tfunction _loadServices(\$di) {\n";
        $file .= implode("\n", $this->services->map(function(ModelService $model){return $model->getServiceCache();})->toArray());
        $file .= "\n\t}\n";
        file_put_contents($cacheDir . self::CACHE_FILE, $file);
    }
    
    /**
     * @param string $className
     * @return string 
     */
    public function getServiceName($className) 
    {
        $model = $this->services->find(function(ModelService $model) use ($className) {return $model->getClassName() == $className;});
        if (!is_null($model)) {
            return $model->getServiceName();
        }
        "";
    }

}
