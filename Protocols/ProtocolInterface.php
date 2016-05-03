<?php
/**
 * The protocol interface file of the framework.
 * Every builtin or self defined protocol should implements this interface.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Protocols;


interface ProtocolInterface {

    /**
     * Provide the transport which the current protocol is using.
     * @return string (udp|tcp)
     */
    public function getTransport();

    /**
     *
     * @return mixed
     */
    public function input();

    /**
     * @return mixed
     */
    public function output();

}