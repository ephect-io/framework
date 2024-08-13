<?php

namespace Ephect\Framework\Manifest;

abstract class ManifestReader
{
    abstract public function read(string $manifestDirectory): ManifestEntityInterface;

    protected function readManifest(string $manifestDirectory, bool $asPhpArray = false) : array
    {
        $filename = $manifestDirectory . DIRECTORY_SEPARATOR . 'manifest' . ($asPhpArray ? ".php" : ".json");
        if(!file_exists($filename)) {
            throw new \ErrorException("Manifest '$filename' not found");
        }

        $json = [];
        if($asPhpArray) {
            $json = require $filename;
        } else {
            $json = file_get_contents($filename);
            if(!json_validate($json)) {
                throw new \ErrorException("Manifest '$filename' is not valid");
            }

            $json = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        return $json;
    }
}
