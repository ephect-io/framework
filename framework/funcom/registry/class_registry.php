<?php

namespace FunCom\Registry;

class ClassRegistry extends BaseRegistry
{
    public static function getInstance(): ClassRegistry
    {
        if (self::$instance === null) {
            self::$instance = new ClassRegistry();
        }

        return self::$instance;
    }
}
