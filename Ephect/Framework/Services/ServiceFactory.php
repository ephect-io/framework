<?php

namespace Ephect\Framework\Services;

use Ephect\Framework\Registry\FrameworkRegistry;

class ServiceFactory implements ServiceFactoryInterface
{
     public function create(string $serviceClass): ServiceInterface|null
     {
         $filename = CONFIG_DIR . "factories.php";
         if(!file_exists($filename)) {
             return null;
         }

         $result = null;

         $factories = require_once $filename;
         foreach ($factories as $factoryClass) {
             $factoryFile = FrameworkRegistry::read($factoryClass);
             include_once  $factoryFile;
             if (is_subclass_of($factoryClass, ServiceFactoryInterface::class)) {
                 $factory = new $factoryClass;
                 $result = $factory->create($serviceClass);
                 break;
             }
         }

         return  $result;

     }
}

