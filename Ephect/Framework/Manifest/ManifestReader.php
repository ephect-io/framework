<?php

namespace Ephect\Framework\Manifest;

use Ephect\Framework\Utils\File;

abstract class ManifestReader
{
    abstract public function read(string $manifestDirectory): ManifestEntityInterface;

    /**
     * @throws \JsonException
     */
    protected function readManifest(string $manifestDirectory, bool $asPhpArray = false) : array
    {
        $filename = $manifestDirectory . DIRECTORY_SEPARATOR . 'manifest' . ($asPhpArray ? ".php" : ".json");

        $json = [];
        if($asPhpArray) {
            $json = require $filename;
        } else {
            $json = File::safeRead($filename);
            if(!json_validate($json)) {
                throw new \JsonException("Manifest '$filename' is not valid");
            }

            $json = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        return $json;
    }
}
