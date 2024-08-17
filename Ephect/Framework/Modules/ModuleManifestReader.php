<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestReader;
use Ephect\Framework\Manifest\ManifestReaderInputEnum;

class ModuleManifestReader extends ManifestReader
{

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function read(string $manifestDirectory): ModuleManifestEntity
    {
        // TODO: Implement read() method.
        $jsonArray = $this->readManifest($manifestDirectory, ManifestReaderInputEnum::IS_ARRAY);

        $struct = new ModuleManifestStructure($jsonArray);
        return new ModuleManifestEntity($struct);
    }
}