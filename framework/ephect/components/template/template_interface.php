<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ephect\Web;

use Ephect\ElementInterface;

/**
 * Description of TObject
 *
 * @author david
 */
interface TemplateInterface extends ElementInterface
{
    public function getViewName();
    public function getViewFileName();
    public function getClassName();
}
