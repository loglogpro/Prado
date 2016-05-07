<?php
/**
 * 
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Listeners;


class ReceiveListener 
{
    protected static $onReceiveListener;

    public static function setOnReceiveListener($onReceiveListener)
    {
        self::$onReceiveListener = $onReceiveListener;
    }

    public static function onReceive($receiveData)
    {
        return call_user_func(self::$onReceiveListener, $receiveData);
    }
}