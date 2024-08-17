<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Manifest\ManifestReader;

class ComposerConfigReader extends ManifestReader
{

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function read(string $manifestDirectory): ComposerConfigEntity
    {
        $json = $this->readManifest($manifestDirectory);

        $struct = new ComposerConfigStructure;
        $struct->decode($json);

        return new ComposerConfigEntity($struct);
    }
}