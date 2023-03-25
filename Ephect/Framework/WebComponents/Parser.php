<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\Components\Generators\RawHtmlParser;

class Parser extends RawHtmlParser
{

   private string $template;
   private string $script;
   private string $style;

   public function __construct(private string $html)
   {
      parent::__construct($html);
   }

   public function doTags(): void
   {
      $this->doTag('template');
      $this->template = $this->getInnerHTML();

      $this->doTag('script');
      $this->script = $this->getInnerHTML();

      $this->doTag('style');
      $this->style = $this->getInnerHTML();
   }

   public function getScript(): string
   {
      $heredoc = <<<HTML
      `
      $this->style
      $this->template
      `
      HTML;
      $script = str_replace("document.getElementById('HelloWord').innerHTML", $heredoc, $this->script);

      return $script;
   }
}
