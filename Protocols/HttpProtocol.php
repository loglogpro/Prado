<?php
/**
 * Http Protocol.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Protocols;


class HttpProtocol implements ProtocolInterface
{
    /**
     * Provide the transport which the current protocol is using.
     * @return string (udp|tcp)
     */
    public function getTransport()
    {
        return 'tcp';
    }

    /**
     *
     * @return mixed
     */
    public function encrypt()
    {
        // TODO: Implement encrypt() method.
    }

    /**
     * @return mixed
     */
    public function decrypt()
    {
        // TODO: Implement decrypt() method.
    }
}