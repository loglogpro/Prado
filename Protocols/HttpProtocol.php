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
    public function encrypt($data)
    {
        return $data;
    }

    /**
     * @return mixed
     */
    public function decrypt($data)
    {
        return $data;
    }
}