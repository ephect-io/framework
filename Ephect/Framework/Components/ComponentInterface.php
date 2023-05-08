<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementInterface;

interface ComponentInterface extends ElementInterface
{
    public function getParentHTML(): ?string;

    public function getCode(): ?string;

    public function getFullyQualifiedFunction(): ?string;

    public function getFunction(): ?string;

    public function getDeclaration(): ?ComponentDeclaration;

    public function getEntity(): ?ComponentEntity;

    public function getBodyStart(): int;

    public function resetDeclaration(): void;

    public function composedOf(): ?array;

    public function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null): string;

    public function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array;

    public function render(): void;
}
