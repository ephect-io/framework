<?php

namespace Ephect\Modules\Html\Services;

use DateTime;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ChildrenInterface;

class HtmlSaverService implements HtmlSaverServiceInterface
{

    public function __construct(private readonly ChildrenInterface $children)
    {
    }

    public function canRender(): bool
    {
        $canRender = $this->children->getAttribute('render') ?? true;

        return $canRender;
    }

    public function isPending(): bool
    {
        return file_exists(\Constants::CACHE_DIR . $this->children->getName() . '.pending' . TXT_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $pendingTxt = \Constants::CACHE_DIR . $this->children->getName() . '.pending' . TXT_EXTENSION;
        File::safeWrite($pendingTxt, "const time = $timestamp");
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = DOCUMENT_ROOT . $name . DIRECTORY_SEPARATOR . $name . HTML_EXTENSION;
        File::safeWrite($finalHTML, $html);
    }

}
