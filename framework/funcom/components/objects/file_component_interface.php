<?php

namespace FunCom\Components;

interface FileComponentInterface extends ComponentInterface
{
    function getSourceFilename(): string;
    static function getCacheFilename(string $basename): string;
    function load(string $filename): bool;
    
}
