<?php

namespace mirolabs\phalcon\Framework;

use Phalcon\Config\Adapter\Yaml;
use Phalcon\Logger\Multiple as MultipleStream;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger\Adapter\Stream as StreamAdapter;
use Phalcon\Logger\Adapter\Syslog as SyslogAdapter;
use Phalcon\Logger\Adapter\Firephp as Firephp;
use Phalcon\Logger\Adapter as Adapter;

class Logger
{
    /**
     * @var Logger
     */
    private static $Instance;
    public static $ConfigPath;
    public static $StartTime;

    /**
     * @var MultipleStream 
     */
    private $logger;

    private function __construct()
    {
        $config       = new Yaml(self::$ConfigPath);
        $this->logger = new MultipleStream();

        //info
        $level = 6;
        if ($config->offsetExists('logger') && $config->get('logger')->offsetExists('level')) {
            $level = $config->get('logger')->get('level');
        }
        $this->loadAdapters($config, $level);
    }

    private function loadAdapters(Yaml $config, $level)
    {
        if ($config->offsetExists('logger') && $config->get('logger')->offsetExists('adapters')) {
            $adapters = $config->get('logger')->get('adapters');
            if (!is_null($adapters)) {
                foreach ($adapters->toArray() as $adapter => $param) {
                    $this->logger->push($this->getAdapter($adapter, $param));
                }
            }
        } else {
            //dafault adapters
            $this->logger->push(new FileAdapter('/tmp/ngApp.log'));
            $this->logger->push(new StreamAdapter('php://stdout'));
        }

        foreach ($this->logger->getLoggers() as $logger) {
            $logger->setLogLevel($level);
        }
    }

    private function getAdapter($adapter, $param)
    {
        switch (strtolower($adapter)) {
            case 'file':
                return new FileAdapter($param);
            case 'stream':
                return new StreamAdapter($param);
            case 'syslog':
                if (is_array($param) && array_key_exists('name', $param) && array_key_exists('options', $param)) {
                    return new SyslogAdapter($param['name'], $param['options']);
                }
                return new SyslogAdapter(null);
            case 'firephp':
                return new Firephp("");
        }

        echo 'Logger adapter not exist: '.$adapter.'<br>';
        echo "Available adapters: file, stream, syslog, firephp";

        exit;
    }

    /**
     * @return Logger
     */
    public static function getInstance()
    {
        if (self::$Instance == null) {
            self::$Instance = new Logger();
        }

        return self::$Instance;
    }


    public function begin()
    {
        $loggers = $this->logger->getLoggers();
        array_walk($loggers, [$this, 'beginAdapter']);
    }

    public function commit()
    {
        $loggers = $this->logger->getLoggers();
        array_walk($loggers, [$this, 'commitAdapter']);

    }

    public function rollback()
    {
        $loggers = $this->logger->getLoggers();
        array_walk($loggers, [$this, 'rolbackAdapter']);
    }


    public function critical()
    {
        $this->logger->critical($this->getMessage(func_get_args()));
    }

    public function emergency()
    {
        $this->logger->emergency($this->getMessage(func_get_args()));
    }

    public function debug()
    {
        $this->logger->debug($this->getMessage(func_get_args()));
    }

    public function error()
    {
        $this->logger->error($this->getMessage(func_get_args()));
    }

    public function info()
    {
        $this->logger->info($this->getMessage(func_get_args()));
    }

    public function notice()
    {
        $this->logger->notice($this->getMessage(func_get_args()));
    }

    public function warning()
    {
        $this->logger->warning($this->getMessage(func_get_args()));
    }

    public function alert()
    {
        $this->logger->alert($this->getMessage(func_get_args()));
    }

    public function criticalException(\Exception $exp)
    {
        $message = sprintf("%s\n%s(%d)\n%s", $exp->getMessage(), $exp->getFile(), $exp->getLine(),
            $exp->getTraceAsString());
        $this->logger->critical($message);
    }

    public function exception(\Exception $exp)
    {
        $message = sprintf("%s\n%s(%d)\n%s", $exp->getMessage(), $exp->getFile(), $exp->getLine(),
            $exp->getTraceAsString());
        $this->logger->exception($message);
    }

    /**
     * @param array $params
     * @return string
     */
    private function getMessage($params)
    {
        $message = "EMPTY MESSAGE";
        if (count($params) > 0) {
            $messagePattern = array_shift($params);
            $message        = vsprintf($messagePattern, $params);
        }

        return sprintf("%s m[%.6f]", $message, microtime(true) - self::$StartTime);
    }

    private function beginAdapter($adapter)
    {
        if ($adapter instanceof Adapter) {
            $adapter->begin();
        }
    }

    private function commitAdapter($adapter)
    {
        if ($adapter instanceof Adapter) {
            $adapter->commit();
        }
    }

    private function rollbackAdapter($adapter)
    {
        if ($adapter instanceof Adapter) {
            $adapter->rollback();
        }
    }
}