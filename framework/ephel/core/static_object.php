<?php

namespace Ephel\Core;

use Ephel\Log\Log;

class StaticElement
{
    protected static $logger = null;
    
    private function __construct()
    {
       
    }
    
    public static function getLogger() : Log
    {
        if(self::$logger === null) {
            self::$logger = Log::create();
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