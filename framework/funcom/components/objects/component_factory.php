<?php

namespace FunCom\Components;

use FunCom\Registry\PluginRegistry;
use FunCom\Registry\ViewRegistry;

class ComponentFactory
{
    public static function create(string $fullyQualifiedName): AbstractFileComponent
    {

        $filename = ViewRegistry::read($fullyQualifiedName);
        $isPlugin = $filename === null ? ($filename = PluginRegistry::read($fullyQualifiedName)) !== null : false;

        if ($isPlugin) {
            $uid = PluginRegistry::read($filename);
            $plugin = new Plugin($uid);
            $plugin->load($filename);

            return $plugin;
        }

        $uid = ViewRegistry::read($filename);
        $view = new View($uid);
        $view->load($filename);

        return $view;
    }
}
