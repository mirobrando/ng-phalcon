<?php

namespace mirolabs\phalcon\Task\Builder;

class ClassBuilder
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->filename = $fileName;
    }

    /**
     * @return $this
     */
    public function createPhpFile()
    {
        $this->writeLine("<?php\n");

        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function createNamespace($namespace)
    {
        $this->writeLine(sprintf("namespace %s;\n", $namespace));

        return $this;
    }

    /**
     * @param array $uses
     * @return $this
     */
    public function createUses(array $uses)
    {
        foreach ($uses as $use) {
            $this->writeLine(sprintf("use %s;", $use));
        }
        $this->writeLine('');

        return $this;
    }

    /**
     * @param string $className
     * @param string $extends
     * @param array $implements
     * @param array $annotations
     * @param bool $abstract
     * @return $this
     */
    public function createClass($className, $extends = null, $implements = [], $annotations = [], $abstract = false)
    {
        $extendsText    = $this->getExtendsText($extends);
        $implementsText = $this->getImplementsText($implements);
        if ($abstract) {
            $classType = 'abstract class';
        } else {
            $classType = 'class';
        }

        $this->createAnnotations($annotations);
        $this->writeLine(sprintf("%s %s%s%s", $classType, $className, $extendsText, $implementsText));
        $this->writeLine('{');

        return $this;
    }

    /**
     * @return $this
     */
    public function closeClass()
    {
        $this->writeLine('}');

        return $this;
    }

    public function addProperty($name, $type = 'string', $availability = 'private', $annotations = [])
    {
        if (!is_array($annotations)) {
            $annotations = [];
        }
        $annotations[] = sprintf("@var %s $%s", $type, $name);
        $this->createAnnotations($annotations, "\t");
        $this->writeLine(sprintf("\t%s $%s;\n", $availability, $name));
    }

    public function addMethod($name, array $params, array $body, $returnType = 'void', $availability = 'public',
        $annotations = [])
    {
        if (!is_array($annotations)) {
            $annotations = [];
        }

        foreach ($params as $paramName => $paramType) {
            $annotations[] = sprintf("@param %s $%s", $paramType, $paramName);
        }
        $annotations[] = sprintf("@return %s", $returnType);


        $this->createAnnotations($annotations, "\t");
        $this->writeLine(sprintf(
                "\t%s function %s(%s)", $availability, $name,
                implode(', ',
                    array_map(function ($value) {
                        return '$'.$value;
                    }, array_keys($params)))
        ));
        $this->writeLine("\t{");
        foreach ($body as $line) {
            $this->writeLine("\t\t".$line);
        }
        $this->writeLine("\t}\n");
    }

    /**
     * @param $implements
     * @return string
     */
    private function getImplementsText($implements)
    {
        if (is_array($implements) && !empty($implements)) {
            return ' implements '.implode(', ', $implements);
        }

        return '';
    }

    /**
     * @param $extends
     * @return string
     */
    private function getExtendsText($extends)
    {
        if ($extends != null) {
            return ' extends '.$extends;
        }

        return '';
    }

    /**
     * @param array $annotations
     * @param string $tabs
     */
    private function createAnnotations($annotations = [], $tabs = '')
    {
        $this->writeLine($tabs.'/**');
        if (is_array($annotations)) {
            foreach ($annotations as $annotation) {
                $this->writeLine($tabs.' * '.$annotation);
            }
        }
        $this->writeLine($tabs.' */');
    }

    /**
     * @param string $line
     */
    protected function writeLine($line)
    {
        file_put_contents($this->filename, $line."\n", FILE_APPEND);
    }
}