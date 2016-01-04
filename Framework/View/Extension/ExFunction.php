<?php

namespace mirolabs\phalcon\Framework\View\Extension;

interface ExFunction {
     function getName();
     function setParams(array $params);
     function call();
}
