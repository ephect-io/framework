<?php

namespace FunCom\Core;

use FunCom\Logger\Logger;

class StaticElement
{
    protected static $logger = null;
    
    private function __construct()
    {

    }
    
    public static function getLogger() : Logger
    {
        if(self::$logger === null) {
            self::$logger = Logger::create();
        }
        return self::$logger;
    }

    public static function create(...$params)
    {
        $class = __CLASS__;
        $object = null;
        
        if(count($params) > 0) {
            $object = new $class();
        } else {
            $object = new $class($params);
        }
        
        return $object;
    }
}