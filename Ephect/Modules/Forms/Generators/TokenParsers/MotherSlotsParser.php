<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Generators\ComponentDocument;
use JetBrains\PhpStorm\Deprecated;

/**
 * Deprecated
 */
#[Deprecated("It's useless for now", "useEffect", "0.3")]
class MotherSlotsParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {

        $slotParser = new UseSlotParser($this->component, $this->parent);
        $slotParser->do();

        [$source, $dest] = $slotParser->getResult();

        if ($source === "" || $source === null) {
            return;
        }

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

        $parentNamespace = "namespace " . $parentComponent->getNamespace() . ";\n";
        $parentFilename = $parentComponent->getSourceFilename();
        $functionFilename = $parentFilename;
        $parentDoc = new ComponentDocument($parentComponent);
        $parentDoc->matchAll();

        $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

        $decl0 = $parentNamespace;
        $decl1 = substr($parentHtml, 0, $parentComponent->getBodyStart() + 1);
        $decl3 = substr($parentHtml, $parentComponent->getBodyStart() + 1);

        // Remove useSlot from child component
        if ($dest !== "" && $dest !== null) {
            $this->html = str_replace($source, "", $this->html);

            $uses = '';
            if (count($this->parent->getUses())) {
                foreach ($this->parent->getUses() as $use) {
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
            File::safeWrite(\Constants::CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $parentFilename, $parentHtml);
            File::safeWrite(\Constants::CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $this->component->getSourceFilename(), $this->html);
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::save();
        }

        $this->result = $functionFilename;

    }
}
