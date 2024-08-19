<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestEntityInterface;

interface ModuleManifestEntityInterface extends ManifestEntityInterface
{
    public function getTag(): string;

    public function getName(): string;

    public function getEntrypoint(): ?string;

    public function getTemplates(): string;

}