<?php
namespace FunCom\Template;

use FunCom\Element;
use FunCom\Registry\Registry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;
use FunCom\Web\WebObject;
use FunCom\Web\WebObjectInterface;
use FunCom\Web\WebObjectTrait;

class TemplateEngine extends Element implements WebObjectInterface
{

    use WebObjectTrait;


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

        ob_start();
        eval('?>' . $php);
        $html = ob_get_clean();

        return $html;
    }
}
