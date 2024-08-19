<?php

namespace Ephect\Plugins\HtmlSaver;

interface HtmlSaverServiceInterface
{
    public function canRender(): bool;

    public function isPending(): bool;

    public function markAsPending(): void;

    public function storeHTML(string $html): void;

}
