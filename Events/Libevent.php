<?php
/**
 * 
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Events;


use Prado\Exceptions\EventException;

class Libevent implements  EventInterface
{
    protected $eventBase;
    protected $socketHandler;
    protected $eventFlagMap = array(
        self::EVENT_READ => EV_READ,
        self::EVENT_WRITE => EV_WRITE,
    );

    protected function __construct($socketHandler)
    {
        $this->eventBase = event_base_new();
    }

    public static function with($socketHandler)
    {
        return new static($socketHandler);
    }

    public function add($eventFlag, $callback)
    {
        if (!isset($this->eventFlagMap[$eventFlag])) {
            throw new EventException('Event flag is not exists.');
        }
        $event = event_new();
        event_set($event, $this->socketHandler, $this->eventFlagMap[$eventFlag], $callback, $this->eventBase);
        event_base_set($event, $this->eventBase);
        event_add($event);
        return $this;
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function listen()
    {
        event_base_loop($this->eventBase);
    }

}