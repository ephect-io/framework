<?php

namespace Ephect\Components\Generators;

use Ephect\Cache\Cache;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;

class BlocksParser extends Parser
{

    public function doBlocks(): bool
    {
        $className = $this->view->getFullyQualifiedFunction();

        $doc = new ComponentDocument($this->html);
        $doc->matchAll();

        $firstMatch = $doc->getNextMatch();
        if ($firstMatch !== null && $firstMatch->hasCloser()) {

            $functionName = $firstMatch->getName();
            $parentClassName = ComponentRegistry::read($functionName);
            $parentFilename = ComponentRegistry::read($parentClassName);
            $parentHtml = file_get_contents(SRC_ROOT . $parentFilename);

            // $parentHtml = $this->doHtml($parentHtml);
            // $chldHtml = $this->doHtml();
            $token = '_' . str_replace('-', '', $this->view->getUID());

            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $parentHtml = $parentDoc->replaceMatches($doc, $this->html);

            $parentHtml = str_replace($functionName, $functionName . $token, $parentHtml);

            $childHtml = $this->html;

            $childHtml = str_replace($functionName, $functionName . $token, $childHtml);

            $functionFilename = strtolower($functionName);
            $functionFilename = 'source_' . str_replace($functionFilename, $functionFilename . $token, $parentFilename);
            if ($parentHtml !== '') {
                $cacheFilename = Cache::getCacheFilename($functionFilename);
                Utils::safeWrite($cacheFilename, $parentHtml);

                $cacheFilename = CACHE_DIR . $this->view->getCachedSourceFilename();
                Utils::safeWrite($cacheFilename, $childHtml);

                // ComponentRegistry::write($className, $cacheFilename);
            }
        }

        if ($doc->getCount() > 0) {
        }


        return $this->html !== null;
    }
}
