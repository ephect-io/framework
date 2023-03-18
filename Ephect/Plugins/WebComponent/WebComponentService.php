<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\IO\Utils;
use Ephect\Framework\WebComponents\Manifest;
use Ephect\Framework\WebComponents\ManifestStructure;

class WebComponentService implements WebComponentServiceInterface
{

    public function __construct(private $children)
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

    public function getAttributes(): string
    {
        $props = $this->children->props();

        $props = $props->props ?? $props;

        $args = [];
        foreach ($props as $key => $value) {
            $args[] =  $key . '="' . $value . '"';
        }

        return implode(" ", $args);

    }

    public function readManifest(): Manifest
    {
        $children = $this->children;

        $motherUID = $children->getMotherUID();
        $name = $children->getName();

        $manifestFilename = 'manifest.json';
        $manifestCache = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $manifestFilename;

        if (!file_exists($manifestCache)) {
            copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $manifestFilename, $manifestCache);
        }

        $manifestJson = Utils::safeRead($manifestCache);
        $manifest = json_decode($manifestJson, JSON_OBJECT_AS_ARRAY);

        $struct = new ManifestStructure($manifest);

        return new Manifest($struct);

    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalJs = RUNTIME_JS_DIR . $name . HTML_EXTENSION;
        Utils::safeWrite($finalJs, $html);
    }

}
