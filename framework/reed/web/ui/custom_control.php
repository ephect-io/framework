<?php
namespace Reed\Web\UI;

use FunCom\ElementInterface;
use FunCom\Element;
use Reed\Web\WebObjectInterface;
use Reed\Web\WebObjectTrait;

/**
 * Description of custom_control
 *
 * @author David
 */
abstract class CustomControl extends Element implements WebObjectInterface
{
    use WebObjectTrait;

    public function __construct(ElementInterface $parent)
    {
        parent::__construct($parent);

        $this->fatherTemplate = $parent->getFatherTemplate();
        $this->fatherUID = $parent->getFatherUID();
    }

    protected $isRendered = false;

    public function init(): void
    {
    }

    public function load(): void
    {
    }

    public function view($html)
    {
    }

    public function partialLoad(): void
    {
    }

    public function beforeBinding(): void
    {
    }

    public function afterBinding(): void
    {
    }

    public function parse(): bool
    {
        return false;
    }

    public function renderHtml(): void
    {
    }

    public function displayHtml(): void
    {
    }

    public function renderPhp(): void
    {
    }

    public function getHtml(): string
    {
        ob_start();
        $this->render();
        $html = ob_get_clean();
        
        return $html;
    }

    public function unload(): void
    {
    }
    
    


}
