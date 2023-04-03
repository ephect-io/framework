<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\WebComponents\ManifestEntity;

interface WebComponentServiceInterface
{

    public function getAttributes(): string;
    public function getBody(string $tag): ?string;
    public function readManifest(): ManifestEntity;
    public function storeHTML(string $html): void;
    public function isPending(): bool;
    public function markAsPending(): void;

}
