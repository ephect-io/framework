<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\IO\Utils;
use Exception;

class ManifestWriter
{
    public function __construct(private ManifestStructure $struct, private string $directory)
    {
    }

    public function write(): void
    {
        $destDir = $this->directory;
        $name = $this->struct->class;

        if (!Utils::safeMkDir($destDir)) {
            throw new Exception("$destDir creation failed");
        }

        $destDir = realpath($destDir);

        $json = json_encode($this->struct->toArray(), JSON_PRETTY_PRINT);

        $destDir .= DIRECTORY_SEPARATOR;

        Utils::safeWrite($destDir . DIRECTORY_SEPARATOR . $name . '.manifest.json', $json);
    }
}
