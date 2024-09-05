<?php

namespace Ephect\Forms\Components;

use Ephect\Forms\Components\Application\ApplicationComponent;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Forms\Registry\PluginRegistry;

class ComponentFactory
{
    public static function create(string $fullyQualifiedName, string $motherUID): ApplicationComponent
    {

        $filename = ComponentRegistry::read($fullyQualifiedName);
        $isPlugin = $filename === null && ($filename = PluginRegistry::read($fullyQualifiedName)) !== null;

        if ($isPlugin) {
            $uid = PluginRegistry::read($filename);
            $plugin = new Plugin($uid, $motherUID);
            $plugin->load($filename);

            return $plugin;
        }

        $uid = ComponentRegistry::read($filename);
        $comp = new Component($uid, $motherUID);
        $comp->load($filename);

        return $comp;
    }
}
