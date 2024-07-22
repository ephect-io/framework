<?php

namespace Ephect\Plugins\WebComponent;

interface WebComponentServiceInterface
{

    public function isPending(): bool;

    public function markAsPending(): void;

    public function getBody(string $tag): ?string;

    public function readManifest(): ManifestEntity;

    public function storeHTML(string $html): void;

}
