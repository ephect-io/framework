<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Reed\Template;

use FunCom\Registry\Registry;
use Reed\Web\WebObjectInterface;

/**
 * Description of view
 *
 * @author david
 */
class Template extends CustomTemplate
{
    public function __construct(WebObjectInterface $parent, array $dictionary)
    {
        parent::__construct($parent, $dictionary);

        $this->viewName = $parent->getViewName();

        $this->clonePrimitivesFrom($parent);
        $this->cloneNamesFrom($parent);
        $this->getCacheFileName();
        $this->cacheFileName = $parent->getCacheFileName();
        $this->fatherTemplate = $this;
        $this->viewIsFather = true;
        $this->fatherUID = $this->getUID();

    }
}
