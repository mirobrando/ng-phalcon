<?php

namespace mirolabs\phalcon\Framework\Tasks\Module;


use mirolabs\phalcon\Framework\Tasks\ClassBuilder;
use mirolabs\phalcon\Framework\Tasks\FileBuilder;
use mirolabs\phalcon\Framework\Tasks\Module;
use Phalcon\Db\Adapter;
use Phalcon\Db\Column;
use Phalcon\Exception;

class CreateModelFromDBTask extends Module
{
    const ENTER_TABLE_NAME = 'Enter table name';
    const TABLE_IS_NOT_EXISTS = 'Table isn\'t exists!';

    /**
     * @return Adapter
     */
    private function getDatabase()
    {
        return $this->getDI()->get('db');
    }

    public function runAction($params)
    {

        $projectPath = $params[0];
        $module = $this->getModuleName($projectPath);
        if ($module !== false) {
            $tableName = $this->getTableName();
            if ($tableName !== false) {
                $this->createModel($projectPath, $module, $tableName);
            }
        }
    }

    private function createModel($projectPath, $moduleName, $tableName)
    {
        try {

            $classBuilder =  new ClassBuilder($this->createFileModel($projectPath, $moduleName, $tableName));
            $columns = $this->getDatabase()->describeColumns($tableName);

            $classBuilder
                ->createPhpFile()
                ->createNamespace($moduleName . '\\models')
                ->createUses(['Phalcon\\Mvc\\Model'])
                ->createClass($this->getModelName($tableName), 'Model');

            $this->addProperties($columns, $classBuilder);
            $this->addGettersAndSetters($columns, $classBuilder);
            $this->addColumnMap($columns, $classBuilder);
            $this->addSource($tableName, $classBuilder);
            $classBuilder->closeClass();


        } catch (Exception $e) {
            $this->output()->writelnFormat($e->getMessage(), 'error');
        }
    }


    /**
     * @param string $tableName
     * @param ClassBuilder $classBuilder
     */
    private function addSource($tableName, $classBuilder)
    {
        $classBuilder->addMethod('getSource', [], [sprintf("return '%s';", $tableName) ], 'string');
    }

    /**
     * @param Column[] $columns
     * @param ClassBuilder $classBuilder
     */
    private function addColumnMap($columns, $classBuilder)
    {
        $body = ['return ['];
        foreach ($columns as $column) {
            $body[] = sprintf("\t'%s' => '%s',", $column->getName(), $this->getFieldName($column->getName()));
        }
        $body[] = '];';

        $classBuilder->addMethod('columnMap', [], $body, 'array', 'public', ['Independent Column Mapping']);
    }


    /**
     * @param Column[] $columns
     * @param $classBuilder
     */
    private function addGettersAndSetters($columns, $classBuilder)
    {
        foreach ($columns as $column) {
            $fieldName = $this->getFieldName($column->getName());

            $classBuilder->addMethod(
                'get' . ucfirst($fieldName),
                [],
                [sprintf("return \$this->%s;", $fieldName)],
                $this->getModelType($column->getType()),
                'public'
            );

            $classBuilder->addMethod(
                'set'. ucfirst($fieldName),
                [$fieldName => $this->getModelType($column->getType())],
                [
                    sprintf("\$this->%s = \$%s;", $fieldName, $fieldName),
                    '',
                    "return \$this;"
                ],
                $this->getModelType($column->getType()),
                'public'
            );
        }
    }



    /**
     * @param Column[] $columns
     * @param $classBuilder
     */
    private function addProperties($columns, $classBuilder)
    {
        foreach ($columns as $column) {
            $classBuilder->addProperty(
                $this->getFieldName($column->getName()),
                $this->getModelType($column->getType())
            );
        }
    }


    /**
     * @param string $projectPath
     * @param string $moduleName
     * @param string $tableName
     * @return string
     */
    private function createFileModel($projectPath, $moduleName, $tableName)
    {
        $dirPath = $this->getModulePath($projectPath, $moduleName) . '/models';
        $modelPath = $dirPath . '/' . $this->getModelName($tableName) . '.php';
        $fileBuilder = new FileBuilder();
        $fileBuilder
            ->createFolder($dirPath)
            ->createFile($modelPath);

        return $modelPath;
    }

    /**
     * @param string $tableName
     * @return string
     */
    private function getModelName($tableName)
    {
        $data = explode('_', $tableName);
        $result = '';
        foreach ($data as $part) {
            $result .= ucfirst($part);
        }

        return $result;
    }


    /**
     * @param string $dbField
     * @return string
     */
    private function getFieldName($dbField)
    {
        $data = explode('_', $dbField);
        $result = '';
        foreach ($data as $part) {
            $result .= ucfirst($part);
        }

        return  lcfirst($result);
    }


    /**
     * @param string $dbType
     * @return string
     */
    private function getModelType($dbType)
    {
        if (preg_match('/int/', $dbType)) {
            return 'int';
        }

        return 'string';
    }


    /**
     * @return bool|string
     */
    private function getTableName()
    {
        while(empty($tableName)) {
            $tableName =  $this->input()->getAnswer(self::ENTER_TABLE_NAME, '', $this->getDatabase()->listTables());
            if (empty($tableName)) {
                return false;
            }

            if (!$this->getDatabase()->tableExists($tableName)) {
                $this->output()->writelnFormat(self::TABLE_IS_NOT_EXISTS, 'error');
                $tableName = '';
            }
        }

        return $tableName;
    }

} 