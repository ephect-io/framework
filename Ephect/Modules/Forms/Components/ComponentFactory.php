<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Forms\Application\ApplicationComponent;

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
