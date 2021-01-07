<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\ViewRegistry;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;

class View extends AbstractFileComponent
{

    public function __construct(string $uid = '')
    {
        $this->uid = $uid;
        $this->getUID();
    }

    public function analyse(): void
    {
        parent::analyse();

        ViewRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
        UseRegistry::safeWrite($this->getFunction(), $this->getFullyQualifiedFunction());
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

    public static function renderHTML(string $functionName, ?array $functionArgs = null): string
    {
        parent::renderComponent($functionName, $functionArgs);

        $html = parent::renderHTML($functionName, $functionArgs);

        return $html;
    }

    public static function render(string $functionName, ?array $functionArgs = null, ?string $parent = null): void
    {
        $html =  self::renderHTML($functionName, $functionArgs);

        echo $html;
    }

    public static function bind(string $uid)
    {
        $filename = CACHE_DIR . "render_$uid.php";
        if (null === $html = Utils::safeRead($filename)) {
            CodeRegistry::uncache();

            $body = CodeRegistry::read($uid);
            $body = urldecode($body);

            $prehtml = new PreHtml($body);
            $prehtml->parse();

            $html = $prehtml->getCode();

            Utils::safeWrite($filename, $html);
            
            CodeRegistry::delete($uid);
            CodeRegistry::cache();
        }

        eval('?>' . $html);
    }

    public static function replace(string $functionName, ?array $functionArgs = null, string $uid): void
    {
        if ($functionName === 'Block') {
            echo '';
            return;
        }

        list($functionName, $cacheFilename) = self::findComponent($functionName);

        // TO BE DONE
        $html = '';

        echo $html;
    }
}
