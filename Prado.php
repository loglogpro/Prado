<?php
/**
 * The start file of the framework.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado;

use Prado\Exceptions\PradoException;
use Prado\Listeners\ReceiveListener;

require 'bootstrap.php';

class Prado
{
    protected $transport;
    protected $protocol;
    protected $address;
    protected $port;

    protected function __construct($socketAddress)
    {
        $this->parseSocketAddress($socketAddress);
    }

    public function run()
    {
        $this->checkRuntime();
        $this->checkProtocolExists();
        $this->callServer();
    }

    public static function with($socketAddress)
    {
        return new static($socketAddress);
    }

    /**
     * Set the callback which trigger when the server receive a message.
     * @param $onReceiveListener
     * @return $this
     */
    public function setOnReceiveListener($onReceiveListener)
    {
        ReceiveListener::setOnReceiveListener($onReceiveListener);
        return $this;
    }

    protected function parseSocketAddress($socketAddress)
    {
        $socketAddress = explode(':', $socketAddress);
        if (sizeof($socketAddress) != 3) {
            throw new PradoException('Socket address format is not correct.');
        }
        $this->protocol = strtolower($socketAddress[0]);
        $this->address = $socketAddress[1];
        $this->port = $socketAddress[2];
    }

    protected function checkRuntime()
    {
//        if (!$this->onReceiveListener) {
//            throw new \UnexpectedValueException('OnReceiveListener could not be empty.');
//        }
    }

    /**
     * Check if protocol exists.
     * @return void
     * @throws \UnexpectedValueException
     */
    protected function checkProtocolExists()
    {
        $protocolFilePath = PRADO_PATH . DIRECTORY_SEPARATOR . 'Protocols' . DIRECTORY_SEPARATOR . ucfirst($this->protocol) . 'Protocol.php';
        if (!file_exists($protocolFilePath)) {
            throw new PradoException('Protocol is not exists.');
        }
    }

    /**
     * Call the server for service.
     * @throws \UnexpectedValueException
     */
    protected function callServer()
    {
        $protocolClassName = 'Prado\\Protocols\\' . ucfirst($this->protocol) . 'Protocol';
        $protocolObject = new $protocolClassName;

        $this->transport = $protocolObject->getTransport();
        if (!in_array($this->transport, array('tcp', 'udp'))) {
            throw new PradoException('The protocol\'s transport value is not tcp or udp.');
        }
        $transportServerName = 'Prado\\Servers\\' . ucfirst($this->transport) . 'Server';
        $transportObject = new $transportServerName;
        $transportObject->start($protocolObject, $this->address, $this->port);
    }
}

