<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\ElementInterface;

interface ManifestEntityInterface extends ElementInterface
{
    public function getTag(): string;
    public function getClassName(): string;
    public function getEntrypoint(): string;
    public function getArguments(): array;

}