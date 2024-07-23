<?php

namespace Ephect\Plugins\WebComponent;

use DateTime;
use Ephect\Framework\Components\ChildrenInterface;
use Ephect\Framework\Utils\File;
use Ephect\Plugins\WebComponent\Builder\Parser;
use Ephect\Plugins\WebComponent\Manifest\ManifestEntity;
use Ephect\Plugins\WebComponent\Manifest\ManifestReader;

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
        $componentArgsString = '';
        $props = $this->children->props()->slot;
        if ($props !== null) {
            $componentArgs = [];
            foreach ($props as $key => $value) {
                if($key == 'uid') {
                    continue;
                }
                $componentArgs[] = $key . '="' . $value . '"';
            }

            $componentArgsString = ' ' . implode(" ", $componentArgs);
        }

        return "<$tag$componentArgsString></$tag>";

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
        $classJs = $name . CLASS_JS_EXTENSION;
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
