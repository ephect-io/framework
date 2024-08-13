<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestEntity;
use Ephect\Framework\Manifest\ManifestReader;

class ModuleManifestReader extends ManifestReader
{

    /**
     * @throws \ErrorException
     */
    public function read(string $manifestDirectory): ModuleManifestEntity
    {
        // TODO: Implement read() method.
        $jsonArray = $this->readManifest($manifestDirectory, true);

        $struct = new ModuleManifestStructure($jsonArray);
        return new ModuleManifestEntity($struct);
    }
}