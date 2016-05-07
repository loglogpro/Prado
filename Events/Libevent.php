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
        if (!event_set($event, $this->socketHandler, $eventFlag, array($this, 'callback'), $eventId)) {
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

    public function callback($socketHandler, $eventFlag, $eventId)
    {
        switch ($eventFlag) {
            case EV_READ:
                $this->read($eventId);
                break;
            case EV_PERSIST:
                $this->read($eventId);
                break;
        }
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    protected  function read($eventId)
    {
        var_dump('read');
//        $connection = stream_socket_accept($this->socketHandler);
//        stream_set_blocking($connection, TcpServer::STREAM_NON_BLOCKING);
//        $bufferEvent = event_buffer_new($connection, array($this, 'readCallback'), NULL, array($this, 'error'), $eventId);
//        event_buffer_base_set($bufferEvent, $this->eventBase);
        //event_buffer_timeout_set($bufferEvent, 30, 30);
        //event_buffer_watermark_set($bufferEvent, EV_READ, 0, 0xffffff);
        //event_buffer_priority_set($bufferEvent, 10);
//        event_buffer_enable($bufferEvent, EV_READ | EV_PERSIST | EV_WRITE);
        // we need to save both buffer and connection.
//        $this->connections[$eventId] = $connection;
//        $this->eventBuffers[$eventId] = $bufferEvent;
    }

    public function error($bufferEvent, $error, $eventId) {
        event_buffer_disable($this->eventBuffers[$eventId], EV_READ | EV_WRITE);
        event_buffer_free($this->eventBuffers[$eventId]);
        fclose($this->connections[$eventId]);
    }

    public function readCallback($buffer, $eventId) {
        $data = '';
        while ($read = event_buffer_read($buffer, 10)) {
            $data .= $read;
        }
        call_user_func($this->serverCallbacks[$eventId], $data);
        //event_buffer_write($buffer, "hahahahha1");
        stream_socket_sendto($this->connections[$eventId], $data);
        //event_buffer_free($buffer);
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