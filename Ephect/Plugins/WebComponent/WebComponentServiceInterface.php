<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\WebComponents\Manifest;

interface WebComponentServiceInterface
{

    public function getAttributes(): string;
    public function readManifest(): Manifest;
    public function storeHTML(string $html): void;
    public function isPending(): bool;
    public function markAsPending(): void;

}
