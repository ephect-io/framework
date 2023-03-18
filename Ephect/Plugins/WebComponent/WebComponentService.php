<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\IO\Utils;

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

    public function prepareFiles(): array
    {
        $children = $this->children;

        $motherUID = $children->getMotherUID();
        $name = $children->getName();
        $class = $children->getClass();

        $props = $children->props();

        $props = isset($props->props) ? $props->props : $props;

        $args = [];
        foreach ($props as $key => $value) {
            $args[] =  $key . '="' . $value . '"';
        }

        $args = implode(" ", $args);

        $manifestFilename = 'manifest.json';
        $manifestCache = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $manifestFilename;

        if (!file_exists($manifestCache)) {
            copy(CUSTOM_WEBCOMPONENTS_ROOT . $name . DIRECTORY_SEPARATOR . $manifestFilename, $manifestCache);
        }

        $manifestJson = Utils::safeRead($manifestCache);
        $manifest = json_decode($manifestJson);

        $tag = $manifest->tag;

        return [$tag, $args];
    }

    public function storeHTML(string $html): void
    {
        $children = $this->children;
        $name = $children->getName();
        $finalJs = RUNTIME_JS_DIR . $name . HTML_EXTENSION;
        Utils::safeWrite($finalJs, $html);
    }

}
