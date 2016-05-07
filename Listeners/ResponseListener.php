<?php
/**
 * 
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado\Listeners;


class ResponseListener 
{
    protected static $onResponseListener;

    public static function setOnResponseListener($onResponseListener)
    {
        self::$onResponseListener = $onResponseListener;
    }

    public static function onResponse()
    {
        
    }

}