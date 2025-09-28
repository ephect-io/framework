<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\Components\ChildrenInterface;
use Ephect\Framework\Utils\File;
use Ephect\Framework\WebComponents\ManifestEntity;
use Ephect\Framework\WebComponents\ManifestReader;
use Ephect\Framework\WebComponents\Parser;
use DateTime;

class WebComponentService implements WebComponentServiceInterface
{

    public function __construct(private readonly ChildrenInterface $children)
    {
    }

    public function isPending(): bool
    {
        return file_exists(RUNTIME_JS_DIR . $this->children->getName() . '.pending' . JS_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $pendingJs = RUNTIME_JS_DIR . $this->children->getName() . '.pending' . JS_EXTENSION;
        File::safeWrite($pendingJs, "const time = $timestamp");
    }

    public function getBody(string $tag): ?string
    {
        $uid = '';
        if (!isset($this->children->props()->slot)) {
            $uid = $this->children->getUID();
        } else {
            if (method_exists($this->children->props()->slot, 'getUID')) {
                $uid = $this->children->props()->slot->getUID();
            }
            if (isset($this->children->props()->slot->uid)) {
                $uid = $this->children->props()->slot->uid;
            }
        }
        $muid = $this->children->getMotherUID();
        $name = $this->children->getName();

        $textFilename = CACHE_DIR . $muid . DIRECTORY_SEPARATOR . $name . $uid . '.txt';

        $body = File::safeRead($textFilename);

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

        $runtimeDir = RUNTIME_JS_DIR . $name . DIRECTORY_SEPARATOR;
        File::safeMkDir($runtimeDir);
        $finalJs = $runtimeDir . $name . JS_EXTENSION;
        $classJs = $name . CLASS_MJS_EXTENSION;
        $elementJs = $name . "Element" . JS_EXTENSION;

        $parser = new Parser($html);
        $parser->doTags();
        $script = $parser->getScript($name);

        File::safeWrite($finalJs, $script);
        copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $classJs, $runtimeDir . $classJs);
        copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $elementJs, $runtimeDir . $elementJs);

        if (file_exists(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . "lib")) {
            $libFiles = File::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . "lib");
            File::safeMkDir($runtimeDir . 'lib');
            foreach ($libFiles as $filename) {
                copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . 'lib' . $filename, $runtimeDir . 'lib' . $filename);
            }
        }
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $name . HTML_EXTENSION;
        File::safeWrite($finalHTML, $html);
    }

}
