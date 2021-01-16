<?php
namespace Reed\Template;

use Exception;
use FunCom\Element;

class TemplateLoader extends Element implements TemplateInterface
{
    protected $templatePath = '';
    protected $templateType = ETemplateType::NON_PHINK_TEMPLATE;
    protected $componentIsInternal = false;
    protected $isAJAX = false;
    protected $isPartial = false;

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function getTemplateType()
    {
        return $this->templateType;
    }

    public function isClientTemplate()
    {
        return $this->isAJAX;
    }

    public function isPartialTemplate()
    {
        return $this->isPartial;
    }

    public function isInnerTemplate()
    {
        return $this->componentIsInternal;
    }

    public function __construct(string $templatePath, ?ETemplateType $templateType = null)
    {
        if ($templateType !== null) {
            $this->templateType = $templateType::enum();
        }

        $this->isAJAX = ($this->templateType & ETemplateType::PHINK_CLIENT_TEMPLATE) === ETemplateType::PHINK_CLIENT_TEMPLATE;
        $this->isPartial = ($this->templateType & ETemplateType::PHINK_PARTIAL_TEMPLATE) === ETemplateType::PHINK_PARTIAL_TEMPLATE;
        $this->componentIsInternal = ($this->templateType & ETemplateType::PHINK_INNER_TEMPLATE) === ETemplateType::PHINK_INNER_TEMPLATE;

        $this->templatePath = $templatePath;
        $this->try();
    }

    private function try(): void
    {
        if (!file_exists(dirname($this->templatePath))) {
            throw new Exception('No template can be found on path ' . $this->templatePath . '.');
        }
    }
}
