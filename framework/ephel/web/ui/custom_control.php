<?php
namespace Ephel\Web\UI;

use FunCom\ElementInterface;
use FunCom\Element;
use Ephel\Web\WebObjectInterface;
use Ephel\Web\WebObjectTrait;

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

    public function getHtml(): string
    {
        ob_start();
        $this->render();
        $html = ob_get_clean();
        
        return $html;
    }


}
