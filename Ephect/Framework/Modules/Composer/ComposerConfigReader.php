<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Manifest\ManifestReader;
use Ephect\Framework\Utils\File;
use ErrorException;
use JsonException;

class ComposerConfigReader extends ManifestReader
{

    /**
     * @throws JsonException
     * @throws ErrorException
     */
    public function read(?string $manifestDirectory = null): ComposerConfigEntity
    {
        $json = File::safeRead(siteRoot() . "composer.json");
        $struct = new ComposerConfigStructure;
        $struct->decode($json);

        return new ComposerConfigEntity($struct);
    }
}