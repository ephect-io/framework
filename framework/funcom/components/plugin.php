<?php

namespace Ephect\Components;

use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\PluginRegistry;
use Ephect\Registry\UseRegistry;

class Plugin extends AbstractPlugin
{

    public function __construct(string $uid = '')
    {
        $this->uid = $uid;
        $this->getUID();
    }

    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(PLUGINS_ROOT . $this->filename);

        list($this->namespace, $this->function) = ElementUtils::getFunctionDefinition($this->code);
        if($this->function === null) {
            list($this->namespace, $this->function) = ElementUtils::getClassDefinition($this->code);
        } 
        $result = $this->code !== null;

        return  $result;
    }

    public function analyse(): void
    {
        parent::analyse();

        PluginRegistry::write($this->getFullyQualifiedFunction(), $this->getSourceFilename());
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

    public static function render(string $functionName, ?array $functionArgs = null): void
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
}
