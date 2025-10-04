<?php

namespace Ephect\Modules\WebComponent\Manifest;

use Ephect\Framework\ElementInterface;

interface ManifestEntityInterface extends ElementInterface
{
    public function getTag(): string;

    public function getClassName(): string;

    public function getEntrypoint(): string;

    public function getArguments(): array;

}
