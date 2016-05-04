<?php
/**
 * 
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
    public function input()
    {
        // TODO: Implement input() method.
    }

    /**
     * @return mixed
     */
    public function output()
    {
        // TODO: Implement output() method.
    }

}