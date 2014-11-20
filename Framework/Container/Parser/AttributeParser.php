<?php

namespace mirolabs\phalcon\Framework\Container\Parser;

use Phalcon\Exception;

class AttributeParser
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $value
     * @return string
     */
    public function getClassValue($value)
    {
        $result = $this->parseParameter($value);
        if (!is_null($result)) {
            return $result;
        }

        return $value;
    }

    /**
     * @param $value
     * @return array
     */
    public function getArgumentValue($value)
    {
        return $this->getArgumentService($value);
    }

    /**
     * @param $value
     * @return array|null
     */
    private function getArgumentService($value)
    {
        $result = $this->parseReference($value);
        if (!is_null($result)) {
            return [
                'type' => 'service',
                'name' => $result
            ];
        }

        return $this->getArgumentParameter($value);
    }

    /**
     * @param $value
     * @return array|null
     */
    private function getArgumentParameter($value)
    {
        $result = $this->parseParameter($value);
        if (!is_null($result)) {
            return [
                'type' => 'parameter',
                'value' => $result
            ];
        }

        return $this->getArgumentDefault($value);
    }

    /**
     * @param $value
     * @return array
     */
    private function getArgumentDefault($value)
    {
        return [
            'type' => 'parameter',
            'value' => $value
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Phalcon\Exception
     */
    private function getParameterValue($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key]->getValue();
        }

        throw new Exception('Parameter ' . $key . ' is not exists');
    }

    /**
     * @param $text
     * @return null|string
     */
    private function parseReference($text)
    {
        if (preg_match('/^@([a-zA-Z_0-9-\\\.]+)$/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param $text
     * @return null|string
     */
    private function parseParameter($text)
    {
        if (preg_match('/^%([a-zA-Z_0-9-\\\.]+)%$/', $text, $matches)) {
            return $this->getParameterValue($matches[1]);
        }

        return null;
    }
}
