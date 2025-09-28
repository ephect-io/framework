<?php

namespace Ephect\Framework\Components;

interface FileComponentInterface extends ComponentInterface
{
    static function getFlatFilename(string $basename): string;

    function getSourceFilename(): string;

    function getFlattenFilename(): string;

    function getFlattenSourceFilename(): string;

    function load(string $filename = ''): bool;

    function analyse(): void;

    function parse(): void;

    function copyComponents(array &$list, ?string $motherUID = null, ?ComponentInterface $component = null): ?string;

    function identifyComponents(array &$list, ?string $motherUID = null, ?FileComponentInterface $component = null): void;

}
