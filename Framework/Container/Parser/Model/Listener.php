<?php

namespace mirolabs\phalcon\Framework\Container\Parser\Model;

use mirolabs\phalcon\Framework\Container\Parser\DefinitionBuilder;
use mirolabs\phalcon\Framework\Container\Parser\Output;

class Listener extends Service implements Output
{
    const ATTRIBUTE_EVENTS = 'events';
    const ATTRIBUTE_EVENT_NAME = 'event_name';
    const ATTRIBUTE_EVENT_METHOD = 'event_method';

    /**
     * @param DefinitionBuilder $definitionBuilder
     */
    public function writeDefinition(DefinitionBuilder $definitionBuilder)
    {
        parent::writeDefinition($definitionBuilder);
        foreach ($this->getEvents() as $eventName => $eventMethod) {
            $this->writeEventDefinition($definitionBuilder, $eventName, $eventMethod);
        }
    }

    /**
     * @param DefinitionBuilder $definitionBuilder
     * @param string $eventName
     * @param string $eventMethod
     */
    protected function writeEventDefinition(DefinitionBuilder $definitionBuilder, $eventName, $eventMethod)
    {
        $definitionBuilder->writeLine(sprintf(
            "\t\t\$di->get('listener')->attach('%s', function(\$event, \$component) use (\$di) {",
            $eventName
        ));
        $definitionBuilder->writeLine(sprintf(
            "\t\t\t\$di->get('%s')->%s(\$event, \$component);",
            $this->serviceName,
            $eventMethod
        ));
        $definitionBuilder->writeLine("\t\t});");
    }

    /**
     * @return array
     */
    protected function getEvents()
    {
        $events = [];
        if (array_key_exists(self::ATTRIBUTE_EVENTS, $this->values)) {
            foreach ($this->values[self::ATTRIBUTE_EVENTS] as $event) {
                $this->getSimpleEvent($events, $event);
            }
        } else {
            $this->getSimpleEvent($events, $this->values);
        }

        return $events;
    }

    /**
     * @param array $events
     * @param array $event
     */
    protected function getSimpleEvent(&$events, $event)
    {
        $events[$event[self::ATTRIBUTE_EVENT_NAME]] = $event[self::ATTRIBUTE_EVENT_METHOD];
    }
}
