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

    protected function __construct($socketHandler)
    {
        $this->eventBase = event_base_new();
        $this->socketHandler = $socketHandler;
    }

    public static function with($socketHandler)
    {
        return new static($socketHandler);
    }

    public function add($eventFlag, $callback)
    {
        $eventFlagMap = array(
            self::EVENT_READ => EV_READ | EV_PERSIST,
            self::EVENT_WRITE => EV_WRITE,
        );
        if (!isset($eventFlagMap[$eventFlag])) {
            throw new EventException('Event flag is not exists.');
        }
        $event = event_new();
        event_set($event, $this->socketHandler, $eventFlagMap[$eventFlag], $callback);
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
        $result = event_base_loop($this->eventBase);
        if ($result == 1) {
            throw new EventException('No event registered.');
        } else if ($result != 0) {
            throw new EventException('Event base loop error.');
        }
    }

}