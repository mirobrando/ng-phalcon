<?php

namespace mirolabs\phalcon\Framework\Compile\Plugin;

trait Config {

    public function getValue($name) {
        $value = "";
        $config = $this->getConfig();
        foreach (explode('.', $name) as $part) {
            $value = $config->get($part);
            if (is_null($value)) {
                break;
            }
            $config = $value;
        }
        return $value;
    }
    
}
