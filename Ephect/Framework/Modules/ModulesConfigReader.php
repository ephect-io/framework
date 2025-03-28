<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestReader;
use Ephect\Framework\Utils\File;
use ErrorException;
use JsonException;

use function siteRoot;

class ModulesConfigReader extends ManifestReader
{

    /**
     * @throws JsonException
     * @throws ErrorException
     */
    public function read(?string $manifestDirectory = null): ModulesConfigEntity
    {
        $json = File::safeRead(siteRoot() . "modules.json");
        $struct = new ModulesConfigStructure();

        if ($json !== null) {
            $struct->decode($json);
        }

        return new ModulesConfigEntity($struct);
    }
}