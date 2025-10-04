<?php

namespace Ephect\Forms\Components\Generators\TokenParsers;

use Ephect\Forms\Components\Component;
use Ephect\Forms\Components\Generators\ComponentDocument;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Framework\Utils\File;

class ChildSlotsParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        ComponentRegistry::load();

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
        if (!$parentComponent->load()) {
            $this->result = null;
            return;
        }

        $parentFilename = $parentComponent->getSourceFilename();
        $parentDoc = new ComponentDocument($parentComponent);
        $parentDoc->matchAll();

        $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

        if ($parentHtml !== '') {
            File::safeWrite(CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $parentFilename, $parentHtml);
            File::safeWrite(CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $this->component->getSourceFilename(), $this->html);
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::save();
        }

        $this->result = $parentFilename;
    }
}
