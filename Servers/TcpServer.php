<?php
/**
 * Tcp server.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Servers;


use Prado\Exceptions\ServerException;
use Prado\Listeners\ReceiveListener;
use Prado\Listeners\ResponseListener;

class TcpServer implements ServerInterface
{
    const STREAM_NON_BLOCKING = 0;
    protected $socketAddress;
    protected $socketHandler;
    protected $protocolObject;
    protected $eventObject;

    /**
     * Inject the protocol object and start web server.
     * @param $protocolObject
     * @param $address
     * @param $port
     */
    public function start($protocolObject, $address, $port)
    {
        $this->socketAddress = 'tcp:' . $address . ':' . $port;
        $this->protocolObject = $protocolObject;
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
        if (!stream_set_blocking($this->socketHandler, self::STREAM_NON_BLOCKING)) {
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

        //Use event to listen.
        $eventName = 'Prado\\Events\\' . $eventName;
        $this->eventObject = $eventName::with($this->socketHandler);
        $this->eventObject
            ->addReadEvent(array($this, 'onReceive'))
            ->listen();
    }

    public function onReceive($receiveData)
    {
        $receiveData = $this->protocolObject->decrypt($receiveData);
        $responseData = ReceiveListener::onReceive($receiveData);
        return $this->response($responseData);
    }

    protected function response($responseData)
    {
        $responseData = $this->protocolObject->encrypt($responseData);
        //($responseData);
        return $responseData;
    }
}