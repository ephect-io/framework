<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\Components\ChildrenInterface;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\WebComponents\ManifestEntity;
use Ephect\Framework\WebComponents\ManifestReader;
use Ephect\Framework\WebComponents\Parser;

class WebComponentService implements WebComponentServiceInterface
{

    public function __construct(private ChildrenInterface $children)
    {
    }

    public function isPending(): bool
    {
        return file_exists(RUNTIME_JS_DIR . $this->children->getName() . '.pending' . JS_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        $pendingJs = RUNTIME_JS_DIR . $this->children->getName() . '.pending' . JS_EXTENSION;
        Utils::safeWrite($pendingJs, "const time = $timestamp");
    }

    public function getBody(string $tag): ?string
    {
        $uid = '';
        if(!isset($this->children->props()->slot)) {
            $uid = $this->children->getUID();
        } else {
            if(method_exists($this->children->props()->slot, 'getUID')) {
                $uid = $this->children->props()->slot->getUID();
            } 
            if(isset($this->children->props()->slot->uid)) {
                $uid = $this->children->props()->slot->uid;
            } 
        }
        $muid = $this->children->getMotherUID();
        $name = $this->children->getName();

        $textFilename = CACHE_DIR . $muid . DIRECTORY_SEPARATOR . $name . $uid . '.txt';

        $body = Utils::safeRead($textFilename);

        return $body;

    }

    public function readManifest(): ManifestEntity
    {
        $children = $this->children;

        $motherUID = $children->getMotherUID();
        $name = $children->getName();

        $reader = new ManifestReader($motherUID, $name);

        return $reader->read();
    }

    public function splitHTML(string $html): void
    {
        $name = $this->children->getName();

        $runtimeDir = strtolower(RUNTIME_JS_DIR . $name) . DIRECTORY_SEPARATOR;
        Utils::safeMkDir($runtimeDir);
        $finalJs = $runtimeDir . $name . MJS_EXTENSION;
        $classJs = $name . CLASS_MJS_EXTENSION;

        $parser = new Parser($html);
        $parser->doTags();
        $script = $parser->getScript($name);

        Utils::safeWrite($finalJs, $script);
        copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $classJs, $runtimeDir . $classJs);

        if(file_exists(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR  . "lib")) {
            $libFiles = Utils::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR  . "lib");
            Utils::safeMkDir($runtimeDir . 'lib');
            foreach($libFiles as $filename) {
                copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR  . 'lib' . $filename, $runtimeDir . 'lib' . $filename);
            }
        }
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $name . HTML_EXTENSION;
        Utils::safeWrite($finalHTML, $html);
    }

}
