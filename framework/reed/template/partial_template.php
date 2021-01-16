<?php
 namespace Reed\Template;
 
use Reed\Web\WebObjectInterface;

class PartialTemplate extends CustomTemplate
{
    public function __construct(WebObjectInterface $parent, array $dictionary, ?string $className = null)
    {
        $this->className = $parent->getType();
        if($className !== null) {
            $this->className = $className;
        }
        parent::__construct($parent, $dictionary);

        $this->clonePrimitivesFrom($parent);

        $this->setViewName($this->className);
        $this->setNamespace();
        $this->setNames();
    }
}
