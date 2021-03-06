<?php

namespace Ephect\Components\Generators;

use Ephect\Cache\Cache;
use Ephect\Components\Component;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class BlocksParser extends Parser
{

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
            $parentHtml = file_get_contents(SRC_COPY_DIR . $parentFilename);

            // $parentHtml = $this->doHtml($parentHtml);
            // $chldHtml = $this->doHtml();
            $token = '_' . str_replace('-', '', $this->component->getUID());
            $functionNameToken = $functionName . $token;
            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $parentHtml = $parentDoc->replaceMatches($doc, $this->html);
            $parentHtml = str_replace($functionName, $functionNameToken, $parentHtml);

            $childHtml = $this->html;
            $childHtml = str_replace($functionName, $functionNameToken, $childHtml);

            $functionFilename = strtolower($functionName);
            $functionFilename = '' . Component::getCacheFilename(str_replace($functionFilename, $functionFilename . $token, $parentFilename));
            if ($parentHtml !== '') {
                Utils::safeWrite(CACHE_DIR . $functionFilename, $parentHtml);
                Utils::safeWrite(CACHE_DIR . $this->component->getCachedFilename(), $childHtml);

                ComponentRegistry::write($parentClassName . $token, $functionFilename);
                ComponentRegistry::write($className, $this->component->getCachedFilename());
            }
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        return $functionFilename;
    }
}
