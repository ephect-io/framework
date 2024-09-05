<?php

namespace Ephect\Modules\Html;

interface HtmlSaverServiceInterface
{
    public function canRender(): bool;

    public function isPending(): bool;

    public function markAsPending(): void;

    public function storeHTML(string $html): void;

}
