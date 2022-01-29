<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\Component;
use Ephect\Framework\Components\Generators\ComponentDocument;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Registry\ComponentRegistry;

class ChildSlotsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        ComponentRegistry::uncache();
        $functionFilename = null;

        $motherUID = $this->component->getMotherUID();
        $doc = new ComponentDocument($this->component);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch === null || !$firstMatch->hasCloser()) {
            $this->result = null;
            return;
        }

        $functionName = $firstMatch->getName();

        $parentComponent = new Component($functionName, $motherUID);
        if(!$parentComponent->load()) {
            $this->result = null;
            return;
        }

        $parentFilename = $parentComponent->getFlattenSourceFilename();
        $functionFilename = $parentFilename;
        $parentDoc = new ComponentDocument($parentComponent);
        $parentDoc->matchAll();

        $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

        if ($parentHtml !== '') {
            Utils::safeWrite(CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $parentFilename, $parentHtml);
            Utils::safeWrite(CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $this->component->getFlattenFilename(), $this->html);
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        $this->result = $functionFilename;
    }
}
