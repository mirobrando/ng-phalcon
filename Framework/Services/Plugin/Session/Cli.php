<?php

namespace mirolabs\phalcon\Framework\Services\Plugin\Session;


class Cli {

    public function has() {
        return false;
    }

    public function set($key, $value) {
        return null;
    }

    public function get($key) {
        return null;
    }

}