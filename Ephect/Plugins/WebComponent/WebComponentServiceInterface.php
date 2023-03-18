<?php

namespace Ephect\Plugins\WebComponent;

use Ephect\Framework\IO\Utils;

interface WebComponentServiceInterface
{

    public function prepareFiles(): array;
    public function storeHTML(string $html): void;
    public function isPending(): bool;
    public function markAsPending(): void;

}
