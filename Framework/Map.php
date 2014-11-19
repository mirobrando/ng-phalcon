<?php

namespace mirolabs\phalcon\Framework;


class Map
{
    private $params = [];

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->params[$key] = json_decode($value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
