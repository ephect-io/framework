<?php

namespace Ephect\Modules\WebComponent;

use Ephect\Modules\WebComponent\Manifest\ManifestEntity;

interface WebComponentServiceInterface
{

    public function isPending(): bool;

    public function markAsPending(): void;

    public function getBody(string $tag): ?string;

    public function readManifest(): ManifestEntity;

    public function storeHTML(string $html): void;

}
