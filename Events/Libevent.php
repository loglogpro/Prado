<?php
/**
 * 
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Events;


use Prado\Exceptions\EventException;
use Prado\Servers\TcpServer;

class Libevent implements  EventInterface
{
    protected $eventBase;
    protected $socketHandler;
    protected $eventsStack = array();
    protected $serverCallbacks;
    protected $connections = array();
    protected $eventFlagMap = array();
    protected $eventBuffers = array();

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

    public function addReadEvent($callback)
    {
        return $this->add(EV_READ | EV_PERSIST, $callback);
    }

    protected function add($eventFlag, $callback)
    {
        $eventId = $eventFlag;
        $this->serverCallbacks[$eventId] = $callback;
        $event = event_new();
        if ($event == false) {
            throw new EventException('Event new occurred an error.');
        }
        if (!event_set($event, $this->socketHandler, $eventFlag, array($this, 'onAccept'), $eventId)) {
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

    public function onAccept($socketHandler, $eventFlag, $eventId)
    {
        switch ($eventFlag) {
            case EV_READ:
            case EV_PERSIST:
                $connection = stream_socket_accept($socketHandler);
                stream_set_blocking($connection, TcpServer::STREAM_NON_BLOCKING);
                $bufferEvent = event_buffer_new($connection, array($this, 'onRead'), array($this, 'onWrite'), array($this, 'onError'), $eventId);
                event_buffer_base_set($bufferEvent, $this->eventBase);
                //event_buffer_timeout_set($bufferEvent, 30, 30);
                //event_buffer_watermark_set($bufferEvent, EV_READ, 0, 0xffffff);
                //event_buffer_priority_set($bufferEvent, 10);
                event_buffer_enable($bufferEvent, EV_READ | EV_PERSIST | EV_WRITE);
                //We need to save both buffer and connection.
                $this->connections[$eventId] = $connection;
                $this->eventBuffers[$eventId] = $bufferEvent;
                break;
        }
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function onRead($bufferEvent, $eventId)
    {
        $data = '';
        while ($read = event_buffer_read($bufferEvent, 256)) {
            $data .= $read;
        }
        $data = call_user_func($this->serverCallbacks[$eventId], $data);
        event_buffer_write($bufferEvent, $data);
    }

    public function onWrite($bufferEvent, $eventId)
    {
        event_buffer_disable($bufferEvent, EV_READ | EV_WRITE | EV_PERSIST);
        event_buffer_free($bufferEvent);
        fclose($this->connections[$eventId]);
    }

    protected function closeConnection()
    {

    }

    public function onError($bufferEvent, $error, $eventId) {
        event_buffer_disable($bufferEvent, EV_READ | EV_WRITE | EV_PERSIST);
        event_buffer_free($bufferEvent);
        fclose($this->connections[$eventId]);
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