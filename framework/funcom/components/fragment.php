<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;

class Fragment extends AbstractComponent
{
    public function __construct(string $uid, string $parentHTML)
    {
        CodeRegistry::uncache();

        $this->parentHTML = $parentHTML;        

        $this->code = CodeRegistry::read($uid);
        $this->code = urldecode($this->code);
    }

    public function analyse(): void
    {
    }

    public function parse(): void 
    {
        $parser = new Parser($this);
        $parser->doScalars();
        $parser->useVariables();

        $html = $this->parentHTML;
        
        $parentBlocks = $parser->doChildren('Block', $this->parentHTML);
        
        $thisBlocks = $parser->doChildren('Block', $this->code);

        $names = array_keys($thisBlocks);

        foreach($names as $name) {
            if(isset($parentBlocks[$name])) {
                $html = str_replace($parentBlocks[$name]->component, $thisBlocks[$name]->body, $html);
            }
        }


        $this->parentHTML = $html;

    }
}
