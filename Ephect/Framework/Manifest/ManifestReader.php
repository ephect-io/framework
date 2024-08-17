<?php

namespace Ephect\Framework\Manifest;

use Ephect\Framework\Utils\File;

abstract class ManifestReader
{
    abstract public function read(string $manifestDirectory): ManifestEntityInterface;

    /**
     * @throws \JsonException
     */
    protected function readManifest(
        string $manifestDirectory,
        ManifestReaderInputEnum $inputOption = ManifestReaderInputEnum::IS_OBJECT,
        ManifestReaderOutputEnum $returnOption = ManifestReaderOutputEnum::AS_IS
    ): array|string|null
    {
        $asPhpArray = $inputOption == ManifestReaderInputEnum::IS_ARRAY;
        $filename = $manifestDirectory . DIRECTORY_SEPARATOR . 'manifest' . ($asPhpArray ? ".php" : ".json");

        $result = null;
        if($asPhpArray) {
            $result = require $filename;

            if($returnOption == ManifestReaderOutputEnum::AS_STRING) {
                $result = json_encode($result, JSON_PRETTY_PRINT);
            }
        } else {
            $result = File::safeRead($filename);
            if(!json_validate($result)) {
                throw new \JsonException("Manifest '$filename' is not valid");
            }
            if($returnOption == ManifestReaderOutputEnum::AS_STRING) {
                return $result;
            }

            $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        }

        return $result;
    }
}
