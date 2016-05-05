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
    protected $eventsStack = array();

    protected function __construct($socketHandler)
    {
        $this->eventBase = event_base_new();
        if ($this->eventBase == false) {
            throw new EventException('Event base new occurred an error.');
        }
        $this->socketHandler = $socketHandler;
    }

    public static function with($socketHandler)
    {
        return new self($socketHandler);
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
        if ($event == false) {
            throw new EventException('Event new occurred an error.');
        }
        if (!event_set($event, $this->socketHandler, $eventFlagMap[$eventFlag], $callback)) {
            throw new EventException('Event set occurred an error.');
        }
        if (!event_base_set($event, $this->eventBase)) {
            throw new EventException('Event base set occurred an error.');
        }
        if (!event_add($event)) {
            throw new EventException('Event add occurred an error.');
        }
        //The event must push into the stack, or event_base_loop will find no event.
        $this->eventsStack[] = $event;
        return $this;
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function read()
    {

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