<?php

namespace FunCom;

use FunCom\Logger\Logger;

class StaticElement
{
    protected static $instance = null;
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
        self::$instance = null;
        
        if(count($params) > 0) {
            self::$instance = new $class();
        } else {
            self::$instance = new $class($params);
        }
        
        return self::$instance;
    }
}