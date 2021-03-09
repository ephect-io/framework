<?php

namespace Ephect\Components;

use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\CodeRegistry;

class Component extends AbstractFileComponent
{

    public function __construct(string $uid = '')
    {
        $this->uid = $uid;
        $this->getUID();
    }

    public function analyse(): void
    {
        parent::analyse();

        ComponentRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        ComponentRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
    }

    public function parse(): void
    {
        parent::parse();

        $this->cacheHtml();
    }

    private function cacheHtml(): ?string
    {
        $cache_file = static::getCacheFilename($this->filename);
        $result = Utils::safeWrite(CACHE_DIR . $cache_file, $this->code);

        return $result === null ? $result : $cache_file;
    }

    public static function render(string $functionName, ?array $functionArgs = null, ?string $parent = null): void
    {
        [$namespace, $functionName, $html] = parent::renderComponent($functionName, $functionArgs);

        $html = parent::renderHTML($functionName, $functionArgs);
        echo $html;
    }

    public static function bind(string $uid)
    {
        $filename = CACHE_DIR . "render_$uid.php";
        if (null === $html = Utils::safeRead($filename)) {
            CodeRegistry::uncache();

            $html = CodeRegistry::read($uid);
            $html = urldecode($html);
         
            Utils::safeWrite($filename, $html);
            
            CodeRegistry::delete($uid);
            CodeRegistry::cache();
        }

        eval('?>' . $html);
    }

}
