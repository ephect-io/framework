<?php

namespace Ephect\Components\Generators;

// use Ephect\Cache\Cache;
// use Ephect\Components\Component;
use Ephect\Components\FileComponentInterface;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class BlocksParser extends Parser
{
    protected $blockComponent;

    public function __construct(FileComponentInterface $comp)
    {
        $this->blockComponent = $comp;
        parent::__construct($comp);
    }

    public function doBlocks(): ?string
    {
        ComponentRegistry::uncache();
        $functionFilename = null;

        $doc = new ComponentDocument($this->html);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch === null || !$firstMatch->hasCloser()) {
            return null;
        }

        $functionName = $firstMatch->getName();
        $parentClassName = ComponentRegistry::read($functionName);
        if ($parentClassName === null) {
            return null;
        }
        $parentFilename = ComponentRegistry::read($parentClassName);
        $functionFilename = $parentFilename;
        $parentHtml = Utils::safeRead(CACHE_DIR . $parentFilename);

        $parentDoc = new ComponentDocument($parentHtml);
        $parentDoc->matchAll();

        $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

        if ($parentHtml !== '') {
            Utils::safeWrite(CACHE_DIR . $parentFilename, $parentHtml);
            Utils::safeWrite(CACHE_DIR . $this->blockComponent->getFlattenFilename(), $this->html);
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        return $functionFilename;
    }
}
