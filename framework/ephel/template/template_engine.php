<?php
namespace Ephel\Template;

use FunCom\Registry\Registry;
use Ephel\Web\UI\CustomControl;
use Ephel\Web\WebObjectTrait;

class TemplateEngine extends CustomControl
{

    use WebObjectTrait;

    protected $templateContents = '';

    public function getTemplate(): string
    {
        return $this->templateContents;
    }

    public function __construct(TemplateLoader $loader)
    {
        $this->path = $loader->getTemplatePath();
        $this->componentIsInternal = $loader->isInnerTemplate();
        $this->isAJAX = $loader->isClientTemplate();
        $this->isPartial = $loader->isPartialTemplate();

    }

    public function render(string $templateName, array $dictionary): string
    {
        $info = (object) \pathinfo($this->path . $templateName);
        $this->viewName = $info->filename;
        $this->dirName = $info->dirname;
        $this->bootDirName = $info->dirname;

        if ($this->componentIsInternal) {
            $this->dirName = dirname($this->dirName, 2);
        }

        $this->className = ucfirst($this->viewName);

        $this->setNamespace();
        $this->setNames();

        $template = new Template($this, $dictionary);
        $template->parse();
        $creations = $template->getCreations();
        $declarations = $template->getAdditions();
        $php = $template->getViewHtml();

        Registry::write('php', $template->getUID(), $php);

        $filename = CACHE_DIR . $this->viewName . PREHTML_EXTENSION;

        file_put_contents($filename, $php);

        ob_start();
        eval('?>' . $php);
        $html = ob_get_clean();

        return $html;
    }
}
