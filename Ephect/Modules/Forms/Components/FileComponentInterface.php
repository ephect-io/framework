<?php

namespace Ephect\Modules\Forms\Components;

interface FileComponentInterface extends ComponentInterface
{
    public static function getFlatFilename(string $basename): string;

    public function getSourceFilename(): string;

    public function getStandardFilename(): string;

    public function getFlattenFilename(): string;

    public function getFlattenSourceFilename(): string;

    public function load(string $filename = ''): bool;

    public function analyse(): void;

    public function parse(): void;

    public function copyComponents(
        array &$list,
        ?string $motherUID = null,
        ?ComponentInterface $component = null
    ): ?string;
}
