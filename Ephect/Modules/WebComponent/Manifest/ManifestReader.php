<?php

namespace Ephect\Modules\WebComponent\Manifest;

use Ephect\Framework\Utils\File;
use Ephect\Modules\WebComponent\Common;

class ManifestReader
{
    public function __construct(private readonly string $motherUID, private readonly string $name)
    {
    }

    public function read(): ManifestEntity
    {

        $manifestFilename = $this->name . '.' . 'manifest.json';
        $manifestCache = \Constants::BUILD_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $manifestFilename;

        if (!file_exists($manifestCache)) {
            $common = new Common();
            copy(
                $common->getCustomWebComponentRoot() . $this->name . DIRECTORY_SEPARATOR . $manifestFilename,
                $manifestCache
            );
        }

        $manifestJson = File::safeRead($manifestCache);
        $manifest = json_decode($manifestJson, JSON_OBJECT_AS_ARRAY);

        $struct = new ManifestStructure($manifest);

        return new ManifestEntity($struct);
    }
}
