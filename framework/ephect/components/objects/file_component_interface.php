<?php

namespace Ephect\Components;

interface FileComponentInterface extends ComponentInterface
{
    function getSourceFilename(): string;
    static function getCacheFilename(string $basename): string;
    function getCachedSourceFilename(): string;
    function load(string $filename): bool;
    
}
