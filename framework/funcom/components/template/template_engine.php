<?php
namespace FunCom\Template;

use FunCom\Cache\Cache;
use FunCom\Element;
use FunCom\IO\Utils;
use FunCom\Registry\Registry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;
use FunCom\Web\TemplateInterface;
use FunCom\Web\TemplateTrait;

class TemplateEngine extends Element implements TemplateInterface
{
    use TemplateTrait;

    public function __construct(string $templateName)
    {
        $this->viewName = $templateName;
        UseRegistry::uncache();
        ViewRegistry::uncache();
    }

    public function render(array $dictionary): string
    {
        $this->className = UseRegistry::read($this->viewName);
        $this->viewName = strtolower($this->viewName);
        $this->viewFileName = ViewRegistry::read($this->className);

        $template = new Template($this, $dictionary);
        $template->parse();
        $php = $template->getViewHtml();

        Registry::write('php', $template->getUID(), $php);

        $cacheFile = Cache::getCacheFilename($this->viewFileName);

        Utils::safeWrite($cacheFile, $php);
        
        ob_start();
        eval('?>' . $php);
        $html = ob_get_clean();

        return $html;
    }
}
