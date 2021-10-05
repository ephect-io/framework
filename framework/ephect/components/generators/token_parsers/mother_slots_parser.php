<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\Component;
use Ephect\Components\Generators\ComponentDocument;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class MotherSlotsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {

        $slotParser = new UseSlotParser($this->component, $this->parent);
        $slotParser->do();

        [$source, $dest] = $slotParser->getResult();

        if($source === "" || $source === null) {
            return;
        }

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

        $parentNamespace = "namespace " . $parentComponent->getNamespace() . ";\n";
        $parentFilename = $parentComponent->getFlattenSourceFilename();
        $functionFilename = $parentFilename;
        $parentDoc = new ComponentDocument($parentComponent);
        $parentDoc->matchAll();

        $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

        $decl0 = $parentNamespace;
        $decl1 = substr($parentHtml, 0, $parentComponent->getBodyStart() + 1);
        $decl3 = substr($parentHtml, $parentComponent->getBodyStart() + 1);

        // Remove useSlot from child component
        if($source !== "" && $source !== null && $dest !== "" && $dest !== null) {
            $this->html = str_replace($source, "", $this->html);

            $uses = '';
            if(count($this->parent->getUses())) {
                foreach($this->parent->getUses() as $use) {
                    $uses .= "\tuse " . $use . ";\n";
                }
    
                $uses = "\n" . $uses;
            }

            $decl0 .= $uses;

            $decl1 = str_replace($parentNamespace, $decl0, $decl1);
            
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
