<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\Utils\File;
use Exception;

class ManifestWriter
{
    public function __construct(private readonly ManifestStructure $struct, private readonly string $directory)
    {
    }

    /**
     * @throws Exception
     */
    public function write(): void
    {
        $destDir = $this->directory;
        $name = $this->struct->class;

        if (!File::safeMkDir($destDir)) {
            throw new Exception("$destDir creation failed");
        }

        $destDir = realpath($destDir);

        $json = json_encode($this->struct->toArray(), JSON_PRETTY_PRINT);

        $destDir .= DIRECTORY_SEPARATOR;

        File::safeWrite($destDir . DIRECTORY_SEPARATOR . $name . '.manifest.json', $json);
    }
}
