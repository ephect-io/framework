<?php

namespace Ephect\Components\Generators;

use Ephect\Cache\Cache;
use Ephect\Components\Component;
use Ephect\Components\FileComponentInterface;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class BlocksParser extends Parser
{
    public function __construct(FileComponentInterface $comp)
    {
        $this->component = $comp;
        parent::__construct($comp);
    }

    public function doBlocks(): ?string
    {
        ComponentRegistry::uncache();
        $functionFilename = null;
        $className = $this->component->getFullyQualifiedFunction();

        $doc = new ComponentDocument($this->html);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->hasCloser()) {

            $functionName = $firstMatch->getName();
            $parentClassName = ComponentRegistry::read($functionName);
            if($parentClassName === null) {
                return null;
            }
            $parentFilename = ComponentRegistry::read($parentClassName);
            $parentHtml = Utils::safeRead(CACHE_DIR . $parentFilename);

            $token = '_' . str_replace('-', '', $this->component->getUID());
            $functionNameToken = $functionName . $token;
            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

            $childHtml = $this->html;
            if ($parentHtml !== '') {
                Utils::safeWrite(CACHE_DIR . $parentFilename, $parentHtml);
                Utils::safeWrite(CACHE_DIR . $this->component->getFlattenFilename(), $childHtml);

            }
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        return $functionFilename;
    }
}
