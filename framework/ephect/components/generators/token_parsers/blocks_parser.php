<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\Component;
use Ephect\Components\Generators\ComponentDocument;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class BlocksParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        ComponentRegistry::uncache();
        $functionFilename = null;

        $doc = new ComponentDocument($this->component);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch === null || !$firstMatch->hasCloser()) {
            $this->result = null;
            return;
        }

        $functionName = $firstMatch->getName();

        $parentComponent = new Component($functionName);
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
            Utils::safeWrite(COPY_DIR . $parentFilename, $parentHtml);
            Utils::safeWrite(COPY_DIR . $this->component->getFlattenFilename(), $this->html);
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        $this->result = $functionFilename;
    }
}
