<?php

namespace Ephect\Plugins\WebComponent;

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

        $destDir = realpath($destDir) . DIRECTORY_SEPARATOR;
        $json = json_encode($this->struct->toArray(), JSON_PRETTY_PRINT);

        File::safeWrite($destDir . DIRECTORY_SEPARATOR . $name . '.manifest.json', $json);
    }
}
