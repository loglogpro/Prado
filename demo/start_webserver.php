<?php
/**
 * The start file of webserver demo.
 * @author    Mrqi<https://github.com/mrqi>
 */

require '../Prado.php';

\Prado\Prado::with('http://127.0.0.1:8888')
    ->setOnReceiveListener(function(){echo 'hee';})
    ->run();