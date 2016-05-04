<?php
/**
 * Tcp server.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Servers;


use Prado\Exceptions\ServerException;
use Prado\Events\EventInterface;

class TcpServer implements ServerInterface
{
    protected $socketAddress;
    protected $socketHandler;

    public function start($protocolObject, $address, $port)
    {
        $this->socketAddress = 'tcp:' . $address . ':' . $port;
        $this->createServer();

    }

    protected function createServer()
    {
        $errCode = 0;
        $errMessage = '';
        $this->socketHandler = stream_socket_server($this->socketAddress, $errCode, $errMessage);
        if (!$this->socketHandler) {
            throw new ServerException($errMessage . '(code:' . $errCode . ')');
        }
        if (!stream_set_blocking($this->socketHandler, 0)) {
            throw new ServerException('Stream set block failed');
        }
        $this->listen();
    }

    protected function listen()
    {
        $eventsHandlerMap = array(
            'Libevent',
        );
        foreach ($eventsHandlerMap as $eventName) {
            if (!extension_loaded($eventName)) {
                unset($eventName);
                continue;
            }
        }
        if (empty($eventName)) {
            throw new ServerException('The current runtime environment could not support event.');
        }
        $eventName = 'Prado\\Events\\' . $eventName;
        $callbackName = 'Prado\\Listeners\\ReceiveListener::onReceive';
        $eventName::with($this->socketHandler)
            ->add(EventInterface::EVENT_READ, $callbackName)
            ->listen();
    }
}