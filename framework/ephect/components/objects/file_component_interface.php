<?php

namespace Ephect\Components;

interface FileComponentInterface extends ComponentInterface
{
    function getSourceFilename(): string;
    static function getFlatFilename(string $basename): string;
    function getFlattenFilename(): string;
    function getFlattenSourceFilename(): string;
    function load(string $filename): bool;
    
}
