<?php

namespace Ephect\Modules\WebComponent\Services;

use DateTime;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ChildrenInterface;
use Ephect\Modules\WebComponent\Builder\Parser;
use Ephect\Modules\WebComponent\Common;
use Ephect\Modules\WebComponent\Manifest\ManifestEntity;
use Ephect\Modules\WebComponent\Manifest\ManifestReader;

final class WebComponentService implements WebComponentServiceInterface
{
    private readonly string $customWebcomponentRoot;
    public function __construct(private readonly ChildrenInterface $children)
    {
        $common = new Common();
        $this->customWebcomponentRoot = $common->getCustomWebComponentRoot();
    }

    public function isPending(): bool
    {
        return file_exists(\Constants::RUNTIME_JS_DIR . $this->children->getName() . '.pending' . \Constants::JS_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $pendingJs = \Constants::RUNTIME_JS_DIR . $this->children->getName() . '.pending' . \Constants::JS_EXTENSION;
        File::safeWrite($pendingJs, "const time = $timestamp");
    }

    public function getBody(string $tag): ?string
    {
        $componentArgsString = '';
        $props = $this->children->props()->slot;
        if ($props !== null) {
            $componentArgs = [];
            foreach ($props as $key => $value) {
                if ($key == 'uid') {
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

        $runtimeDir = \Constants::RUNTIME_JS_DIR . $name . DIRECTORY_SEPARATOR;
        File::safeMkDir($runtimeDir);
        $finalJs = $runtimeDir . $name . \Constants::JS_EXTENSION;
        $classJs = $name . \Constants::CLASS_JS_EXTENSION;
        $elementJs = $name . "Element" . \Constants::JS_EXTENSION;

        $parser = new Parser($html);
        $parser->doTags();
        $script = $parser->getScript($name);

        File::safeWrite($finalJs, $script);
        copy($this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . $classJs, $runtimeDir . $classJs);
        copy($this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . $elementJs, $runtimeDir . $elementJs);

        if (file_exists($this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . "lib")) {
            $libFiles = File::walkTreeFiltered($this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . "lib");
            File::safeMkDir($runtimeDir . 'lib');
            foreach ($libFiles as $filename) {
                copy(
                    $this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . 'lib' . $filename,
                    $runtimeDir . 'lib' . $filename
                );
            }
        }
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = $this->customWebcomponentRoot . $name . DIRECTORY_SEPARATOR . $name . \Constants::HTML_EXTENSION;
        File::safeWrite($finalHTML, $html);
    }
}
