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

            // $parentHtml = $this->doHtml($parentHtml);
            // $chldHtml = $this->doHtml();
            // $token = '_' . str_replace('-', '', $this->component->getUID());
            // $functionNameToken = $functionName . $token;
            $parentDoc = new ComponentDocument($parentHtml);
            $parentDoc->matchAll();

            $parentHtml = $parentDoc->replaceMatches($doc, $this->html);
            // $parentHtml = str_replace($functionName, $functionNameToken, $parentHtml);

            $childHtml = $this->html;
            // $childHtml = str_replace($functionName, $functionNameToken, $childHtml);

            // $functionFilename = strtolower($functionName);
            // $functionFilename = '' . Component::getFlatFilename(str_replace($functionFilename, $functionFilename . $token, $parentFilename));
            if ($parentHtml !== '') {
                Utils::safeWrite(CACHE_DIR . $parentFilename, $parentHtml);
                Utils::safeWrite(CACHE_DIR . $this->blockComponent->getFlattenFilename(), $childHtml);

                // ComponentRegistry::write($parentClassName . $token, $functionFilename);
                // ComponentRegistry::write($className, $this->component->getFlattenFilename());
            }
        }

        if ($doc->getCount() > 0) {
            ComponentRegistry::cache();
        }

        return $functionFilename;
    }
}
