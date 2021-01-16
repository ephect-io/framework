<?php
namespace Reed\Template;

interface TemplateInterface
{
    public function getTemplatePath();
    public function getTemplateType();
    public function isClientTemplate();
    public function isPartialTemplate();
    public function isInnerTemplate();
}
