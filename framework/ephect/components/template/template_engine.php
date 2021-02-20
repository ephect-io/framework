<?php
namespace Ephect\Template;

use Ephect\Cache\Cache;
use Ephect\Element;
use Ephect\IO\Utils;
use Ephect\Registry\Registry;
use Ephect\Registry\UseRegistry;
use Ephect\Registry\ViewRegistry;
use Ephect\Web\TemplateInterface;
use Ephect\Web\TemplateTrait;

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
