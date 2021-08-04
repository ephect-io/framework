<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\Component;
use Ephect\Components\Generators\ComponentDocument;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class SlotsParser extends AbstractTokenParser
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

        $slotParser = new UseSlotParser($this->component);
        $slotParser->do();

        [$source, $dest] = $slotParser->getResult();
        $vars = $slotParser->getVariables();

        $useVars = array_values($vars);
        $use = count($useVars) > 0 ? 'use(' . implode(', ', $vars) . ') ' : '';


        if(strpos($parentHtml, "return function () {") > -1) {
            $parentHtml = str_replace("return function () {", 'return function () ' . $use . ' {', $parentHtml);
        }
        if($p1 = strpos($parentHtml, "return function () use(") > -1) {
            $p1 += 23;
            $p2 = strpos($parentHtml, ")", $p1);
            $use1 = substr($parentHtml, $p1, $p2 - $p1);
            $use2 = $use1 . implode(', ', $vars);

            $parentHtml = str_replace($use1, $use2, $parentHtml);
        }

        $decl1 = substr($parentHtml, 0, $parentComponent->getBodyStart() + 1);
        $decl3 = substr($parentHtml, $parentComponent->getBodyStart() + 1);


        // Remove useSlot from child component
        if($source !== "" && $source !== null && $dest !== "" && $dest !== null) {
            $this->html = str_replace($source, "", $this->html);

            // Add useSlot in mother component
            $parentHtml = $decl1 . "\n\t" . $dest . "\n" . $decl3;

        }


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
