<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

class Task extends Service
{
    const ATTRIBUTE_CLASS_ACTION = 'action';
    const ATTRIBUTE_TASK_DESCRIPTION = 'description';
    /**
     * @return array
     */
    public function getTaskValue()
    {
        return [
            'class' => $this->getClassName(),
            'action' => $this->attributeParser->getClassValue($this->values[self::ATTRIBUTE_CLASS_ACTION]),
            'description' => $this->getDescription(),
            'params' => $this->getArguments()
        ];
    }

    /**
     * @return string
     */
    private function getDescription()
    {
        if (array_key_exists(self::ATTRIBUTE_TASK_DESCRIPTION, $this->values)) {
            return $this->values[self::ATTRIBUTE_TASK_DESCRIPTION];
        }

        return '';
    }
}
