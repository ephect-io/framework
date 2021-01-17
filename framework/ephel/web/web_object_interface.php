<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ephel\Web;

use Ephel\Template\Template;
use FunCom\ElementInterface;
use Ephel\Template\TemplateInterface;

/**
 * Description of TObject
 *
 * @author david
 */
interface WebObjectInterface extends TemplateInterface, ElementInterface
{
    public function getViewName();
    public function getCacheFileName();
    public function getFatherTemplate(): ?Template;
    public function getFatherUID(): string;
    public function getTemplatePath();
    public function getTemplateType();
}
