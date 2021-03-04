<?php

namespace Ephect\Components;

use Ephect\Registry\PluginRegistry;
use Ephect\Registry\ComponentRegistry;

class ComponentFactory
{
    public static function create(string $fullyQualifiedName): AbstractFileComponent
    {

        $filename = ComponentRegistry::read($fullyQualifiedName);
        $isPlugin = $filename === null ? ($filename = PluginRegistry::read($fullyQualifiedName)) !== null : false;

        if ($isPlugin) {
            $uid = PluginRegistry::read($filename);
            $plugin = new Plugin($uid);
            $plugin->load($filename);

            return $plugin;
        }

        $uid = ComponentRegistry::read($filename);
        $view = new View($uid);
        $view->load($filename);

        return $view;
    }
}
