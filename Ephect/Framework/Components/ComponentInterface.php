<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementInterface;

interface ComponentInterface extends ElementInterface
{
    public function getCode(): ?string;

    public function applyCode(string $code): void;

    public function getFullyQualifiedFunction(): ?string;

    public function getFunction(): ?string;

    public function getDeclaration(): ?ComponentDeclarationInterface;

    public function getEntity(): ?ComponentEntityInterface;

    public function getBodyStart(): int;

    public function resetDeclaration(): void;

    public function composedOf(): ?array;

    public function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array;

    public function render(): void;
}
