<?php

namespace FunCom\Components;

use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;

class Parser
{
    private $html = '';
    private $view = null;
    private $useVariables = [];

    public function __construct(ComponentInterface $view)
    {
        $this->view = $view;
        $this->html = $view->getCode();
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function doVariables(): bool
    {
        $result = null;

        $re = '/\{\{ ([a-z0-9_\-\>]*) \}\}/m';
        $su = '<?php echo $\1 ?>';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[1];

            $useVar = $variable;
            $arrowPos = strpos($variable, '->');
            if ($arrowPos > -1) {
                $useVar = substr($useVar, 0, $arrowPos);
            }

            $this->useVariables[$useVar] = '$' . $useVar;

            $this->html = str_replace('{{ ' . $variable . ' }}', '<?php echo $' . $variable . ' ?>', $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    public function useVariables(): bool
    {
        $result = false;

        $useVars = array_values($this->useVariables);
        $use = count($useVars) > 0 ? 'use(' . implode(', ', $useVars) . ') ' : '';

        $this->html = str_replace('(<<<HTML', 'function () ' . $use . '{?>', $this->html);
        $this->html = str_replace('HTML);', "<?php\n\t};", $this->html);

        $result = $this->html !== null;

        return $result;
    }

    public function doComponents(): bool
    {
        $result = false;

        $re = '/\<([A-Z0-9][A-Za-z0-9]*)([\S\{\}\(\)\>\'"= ]*)((\s|[^\/\>].))?\/\>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);


        foreach ($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? $match[2] : '';

            $args = '';
            if (trim($componentArgs) !== '') {
                $args = ', ' . $this->doArguments($componentArgs);
            }

            $componentRender = "<?php \FunCom\Components\View::render('$componentName'$args); ?>";

            $this->html = str_replace($component, $componentRender, $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    /**
     * UNDER CONSTRUCTION
     */
    public function doOpenComponents(string $tag = '[A-Z]'): bool
    {
        $result = '';
        CodeRegistry::uncache();

        $re = '/<(' . $tag . '[\w]+)(\b[^>]*)>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)<\/\1>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);


        foreach ($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? trim($match[2]) : '';
            $componentBody = trim($match[3]);

            $args = '';
            if (trim($componentArgs) !== '') {
                $args = $this->doArguments($componentArgs);
            }

            if(empty($componentBody)) {
                continue;
            }

            $uid = uniqid(time(), true);

            $args = ', ' . (($args === null) ? "null" : $args);
            $body = urlencode($componentBody);

            CodeRegistry::write($uid, $body);
            $uid = ", '" . $uid . "'";

            $componentRender = "<?php \FunCom\Components\View::make('$componentName'$args$uid); ?>";
            if ($componentName === 'Block') {
                $componentRender = "<?php \FunCom\Components\View::replace('$componentName'$args$uid); ?>";
            }

            $this->html = str_replace($component, $componentRender, $this->html);
        }
        CodeRegistry::cache();

        $result = $this->html !== null;

        return $result;
    }

    public function doArguments(string $componentArgs): ?string
    {
        $result = '';

        $re = '/([A-Za-z0-9_]*)=("([\S\\\\\" ]*)"|\'([\S\\\\\' ]*)\'|\{([\S\\\\\{\}\(\)=\<\> ]*)\})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[2], 1), 0, -1);

            $result .= '"' . $key . '" => "' . urlencode($value) . '", ';
        }
        $result = ($result === '') ? null : '[' . $result . ']';

        return $result;
    }


    public function doUses(): bool
    {
        $result = false;

        $re = '/use ([A-Za-z0-9\\\\ ]*)\\\\([A-Za-z0-9]*)([ ]*)?;/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[1], '\\');
            $componentFunction = $match[2];

            UseRegistry::write($componentFunction, $componentNamespace . '\\' . $componentFunction);
        }
        return $result;
    }

    public function doUsesAs(): bool
    {
        $result = false;
        $viewNamespace = $this->view->getNamespace();

        $re = '/use ([A-Za-z0-9\\\\ ]*\\\\)?([A-Za-z0-9 ]*) as ([A-Za-z0-9 ]*);/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[1], '\\');
            $componentFunction = $match[2];
            $componentAlias = $match[3];

            $componentNamespace = ($componentNamespace === '') ? $viewNamespace : $componentNamespace;
            $fqFunctionName = $componentNamespace . '\\' . $componentFunction;

            UseRegistry::write($componentAlias, $fqFunctionName);
        }

        return $result;
    }

}
