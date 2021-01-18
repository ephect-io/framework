<?php
namespace FunCom\Web;

trait TemplateTrait
{
    protected $viewFileName = '';
    protected $viewName = '';
    protected $className = '';

    public function __construct(TemplateInterface $parent)
    {
        parent::__construct($parent);
    }

    public function getViewFileName(): string
    {
        return $this->viewFileName;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
