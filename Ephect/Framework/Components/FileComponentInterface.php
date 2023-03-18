<?php

namespace Ephect\Framework\Components;

interface FileComponentInterface extends ComponentInterface
{
    function getSourceFilename(): string;
    static function getFlatFilename(string $basename): string;
    function getFlattenFilename(): string;
    function getFlattenSourceFilename(): string;
    function load(string $filename = ''): bool;
    function copyComponents(array &$list, ?string $motherUID = null, ?ComponentInterface $component = null): ?string;
    function identifyComponents(array &$list, ?string $motherUID = null, ?FileComponentInterface $component = null): void;
    
}
