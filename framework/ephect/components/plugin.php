<?php

namespace Ephect\Components;

use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

class Plugin extends AbstractPlugin
{

    public function __construct(string $uid = '', string $motherUID = '')
    {
        $this->uid = $uid;
        $this->motherUID = ($motherUID === '') ? $uid : $motherUID;
        $this->getUID();
    }

    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(PLUGINS_ROOT . $this->filename);

        list($this->namespace, $this->function) = ElementUtils::getFunctionDefinition($this->code);
        if($this->function === null) {
            list($this->namespace, $this->function) = ElementUtils::getClassDefinition($this->code);
        } 
        $result = $this->code !== null;

        return  $result;
    }

    public function analyse(): void
    {
        parent::analyse();

        PluginRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }

}
