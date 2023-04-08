<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\IO\Utils;

class ManifestReader
{
    public function __construct(private string $motherUID, private string $name)
    {
    }

    public function read(): ManifestEntity
    {

        $manifestFilename = 'manifest.json';
        $manifestCache = CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $this->name . '.' . $manifestFilename;

        if (!file_exists($manifestCache)) {
            copy(CUSTOM_WEBCOMPONENTS_ROOT . $this->name . DIRECTORY_SEPARATOR . $manifestFilename, $manifestCache);
        }

        $manifestJson = Utils::safeRead($manifestCache);
        $manifest = json_decode($manifestJson, JSON_OBJECT_AS_ARRAY);

        $struct = new ManifestStructure($manifest);

        return new ManifestEntity($struct);
    }
}
