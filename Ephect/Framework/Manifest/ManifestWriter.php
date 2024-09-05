<?php

namespace Ephect\Framework\Manifest;

abstract class ManifestWriter
{
    public function __construct(private readonly ManifestStructure $struct)
    {
    }

    abstract public function write(): void;
}
