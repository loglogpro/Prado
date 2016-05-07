<?php
/**
 * The start file of webserver demo.
 * @author    Mrqi<https://github.com/mrqi>
 */

require '../Prado.php';

\Prado\Prado::with('http://0.0.0.0:8888')
    ->setOnReceiveListener(function($data) {
        return $data;
    })
    ->run();