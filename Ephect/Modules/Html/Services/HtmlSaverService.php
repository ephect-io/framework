<?php

namespace Ephect\Modules\Html\Services;

use DateTime;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ChildrenInterface;

use function Ephect\Hooks\useMemory;

class HtmlSaverService implements HtmlSaverServiceInterface
{
    public function __construct(
        private readonly ChildrenInterface $children,
        private string $buildDirectory,
    ) {
        [$this->buildDirectory] = useMemory(get: 'buildDirectory');

    }

    public function canRender(): bool
    {
        $canRender = $this->children->getAttribute('render') ?? true;

        return $canRender;
    }

    public function isPending(): bool
    {

        return file_exists($this->buildDirectory . $this->children->getName() . '.pending' . \Constants::TXT_EXTENSION);
    }

    public function markAsPending(): void
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $pendingTxt = $this->buildDirectory . $this->children->getName() . '.pending' . \Constants::TXT_EXTENSION;
        File::safeWrite($pendingTxt, "const time = $timestamp");
    }

    public function storeHTML(string $html): void
    {
        $name = $this->children->getName();
        $finalHTML = \Constants::DOCUMENT_ROOT . $name . DIRECTORY_SEPARATOR . $name . \Constants::HTML_EXTENSION;
        File::safeWrite($finalHTML, $html);
    }
}
