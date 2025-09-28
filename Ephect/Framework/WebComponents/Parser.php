<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\Components\Generators\RawHtmlParser;

class Parser extends RawHtmlParser
{

    private string $template = '';
    private string $script = '';
    private string $style = '';

    public function __construct(private readonly string $html)
    {
        parent::__construct($html);
    }

    public function doTags(): void
    {
        $this->doTag('template');
        $htmls = $this->getInnerHTML();
        $this->template = count($htmls) ? $htmls[0] : '';

        $this->doTag('script');
        $htmls = $this->getInnerHTML();
        $this->script = count($htmls) ? $htmls[0] : '';

        $this->doTag('style');
        $htmls = $this->getOuterHTML();
        foreach ($htmls as $html) {
            $this->style .= $html . PHP_EOL;
        }
    }

    public function getScript($name): string
    {
        $heredoc = <<<HTML
        `
        $this->style
        $this->template
        `
        HTML;
        $script = str_replace("document.getElementById('$name').innerHTML", $heredoc, $this->script);

        return $script;
    }
}
