<?php
namespace mirolabs\phalcon\Framework\Compile\Plugin;

use Phalcon\Annotations\Adapter as Annotations;
use mirolabs\phalcon\Framework\Compile\AnnotationParser as AnnotationParser;
use mirolabs\phalcon\Framework\Compile\Plugin\Model\Task as ModelTask;
use mirolabs\collection\ArrayList;

class Task  implements \mirolabs\phalcon\Framework\Compile\Plugin {

    const CLASS_ANNOTATION = 'Service';
    const METHOD_ANNOTATION = 'Task';
    const METHOD_ANNOTATION_COMMAND = 'command';
    const METHOD_ANNOTATION_DESCRIPTION = 'description';
    const METHOD_ANNOTATION_GROUP = 'group';
    const CACHE_FILE = '/tasks.php';
    
    
    /**
     * @var Config 
     */
    private $config;
    
    /**
     * @var ArrayList
     */
    private $tasks;

    use \mirolabs\phalcon\Framework\Compile\Plugin\Config;
    
    public function __construct() {
        $this->tasks = new ArrayList('\mirolabs\phalcon\Framework\Compile\Plugin\Model\Task');
    }
    
    public function getConfig() {
        return $this->config;
    }

    public function setConfig(\Phalcon\Config $config) {
        $this->config = $config;
    }
    
    public function parseFile(Annotations $adapter, $className, $module) {
        $parser = new AnnotationParser($adapter->get($className));
        if ($parser->isExistsAnnotationClass(self::CLASS_ANNOTATION)) {
            $this->tasks->add(new ModelTask($parser, $className));
        }
    }
    
    
    
    public function createCache($cacheDir) {
        $file = "<?php\n\n";
        $file .= "\tfunction _getTasksList() {\n";
        $file .= "\t\treturn [\n";
        $file .= implode(",\n", $this->buildTasks());
        $file .= "\t\t];\n";
        $file .= "\n\t}\n";
        file_put_contents($cacheDir . self::CACHE_FILE, $file);
    }

    
    private function buildTasks() {
        $result = [];
        $this->tasks
                ->map(function (ModelTask $model) { return $model->getTasks(); })
                ->filter(function ($data) { return count($data) > 0;})
                ->each(function ($data) use (&$result) { $result = array_merge($result, $data); });
        return $result;        
    }



}
