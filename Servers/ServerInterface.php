<?php
/**
 * The server interface.
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Servers;


interface ServerInterface {

    public function start($protocolObject, $address, $port);
}