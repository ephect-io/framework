<?php

namespace Ephect\Plugins\HtmlSaver;

use Ephect\Framework\Components\ChildrenInterface;
use Ephect\Framework\IO\Utils;

class HtmlSaverService implements HtmlSaveServiceInterface
{

    public function __construct(private ChildrenInterface $children)
    {
    }

    public function canRender(): bool
    {
        $canRender = $this->children->getAttribute('render') ?? true;

        return $canRender;
    }

    public function isPending(): bool
    {
        return file_exists(CACHE_DIR . $this->children->getName() . '.pending' . TXT_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        $pendingTxt = CACHE_DIR . $this->children->getName() . '.pending' . TXT_EXTENSION;
        Utils::safeWrite($pendingTxt, "const time = $timestamp");
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = DOCUMENT_ROOT . $name . DIRECTORY_SEPARATOR . $name . HTML_EXTENSION;
        Utils::safeWrite($finalHTML, $html);
    }

}
