<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;

class ComponentFactory
{
    public static function create(string $fullyQualifiedName, string $motherUID): AbstractFileComponent
    {

        $filename = ComponentRegistry::read($fullyQualifiedName);
        $isPlugin = $filename === null && ($filename = PluginRegistry::read($fullyQualifiedName)) !== null;

        if ($isPlugin) {
            $uid = PluginRegistry::read($filename);
            $plugin = new Plugin($uid, $motherUID);
            $plugin->load($filename);

            return $plugin;
        }

        $isWebcomponent = $filename === null && ($filename = WebComponentRegistry::read($fullyQualifiedName)) !== null;

        if ($isWebcomponent) {
            $uid = WebComponentRegistry::read($filename);
            $webcomponent = new WebComponent($uid, $motherUID);
            $webcomponent->load($filename);

            return $webcomponent;
        }

        $uid = ComponentRegistry::read($filename);
        $comp = new Component($uid, $motherUID);
        $comp->load($filename);

        return $comp;
    }
}
