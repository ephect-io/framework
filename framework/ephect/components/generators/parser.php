<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentInterface;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\FrameworkRegistry;

class Parser
{
    protected $html = '';
    protected $view = null;
    protected $useVariables = [];
    protected $parentHTML = '';
    protected $maker = null;

    public function __construct(ComponentInterface $view)
    {
        $this->view = $view;
        $this->html = $view->getCode();
        $this->parentHTML = $view->getParentHTML();
        $this->maker = new Maker($view);
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function doCache(): bool
    {
        return CodeRegistry::cache();
    }

    public function doUncache(): bool
    {
        return CodeRegistry::uncache();
    }

    public function doScalars(): bool
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

            $uid = $this->view->getUID();

            if ($variable === 'children') {
                $this->html = str_replace('{{ children }}', "<?php \Ephect\Components\View::bind('$uid'); ?>", $this->html);
                continue;
            }

            $this->html = str_replace('{{ ' . $variable . ' }}', '<?php echo $' . $variable . '; ?>', $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    public function doArrays(): bool
    {
        $result = null;

        $re = '/\{\{ \.\.\.([a-z0-9_\-\>]*) \}\}/m';
        $su = '<?php echo print_r($\1, true) ?>';
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

            if ($variable === 'children') {
                continue;
            }

            $this->html = str_replace('{{ ...' . $variable . ' }}', '<?php echo print_r($' . $variable . ', true) ?>', $this->html);
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

        $result = $this->html !== '';

        return $result;
    }

    public function normalizeNamespace(): bool
    {
        $re = '/namespace([ ]+)(\w+)([ ]+)?;([ ]+)?/';
        $subst = 'namespace \\2;';

        $str = $this->html;

        $this->html = preg_replace($re, $subst, $str);

        $result = $this->html !== null;

        return $result;
    }

    public function doComponents(): bool
    {
        $result = false;

        $re = '/<([A-Z][\w]*)([\w\{\}\(\)\'"= ][^\>]*)((\s|[^\/\>].))?\/\>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);


        foreach ($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? $match[2] : '';

            $args = 'null';
            if (trim($componentArgs) !== '') {
                $componentArgs = $this->doArguments($componentArgs);
                $args = Maker::doArgumentsToString($componentArgs);
            }

            $parent = $this->view->getFullyQualifiedFunction();

            $componentRender = "<?php \Ephect\Components\View::render('$componentName', $args, '$parent'); ?>";

            $this->html = str_replace($component, $componentRender, $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }


    /** TO BE DONE on bas of regex101 https://regex101.com/r/QZejMW/2/ */
    public function doFunctionArguments(string $subject): ?array
    {
        $result = [];
        $re = '/((function) ([\w]+)\()?([\,]?[\.]*\$[\w]*)/m';

        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);




        return $result;
    }


    // public function doMake(): void
    // {
    /**    $re = '/<\?php \\\\Ephect\\\\Components\\\\View::make\(\'.*\'\); \?>/m';  */
    //     $subject = $this->html;

    //     preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

    //     foreach ($matches as $match) {
    //         $makeStatement = $match[0];
    //         $subject = str_replace($makeStatement, $this->parentHTML, $subject);
    //     }
    //     $this->html = $subject;
    // }

    public function doArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_]*)=("([\S\\\\\" ]*)"|\'([\S\\\\\' ]*)\'|\{([\S\\\\\{\}\(\)=\<\> ]*)\})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[2], 1), 0, -1);

            $result[$key] = $value;
        }

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

            $fqFunction = $componentNamespace . '\\' . $componentFunction;
            $frameworkUse = FrameworkRegistry::read($fqFunction);
            if ($frameworkUse !== null) {
                continue;
            }

            ComponentRegistry::write($componentFunction, $fqFunction);
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

            ComponentRegistry::write($componentAlias, $fqFunctionName);
        }

        return $result;
    }

    public function doHtml(?string $html = null): ?string
    {

        $result = '';

        $subject = $html === null ? $this->html : $html;

        $re = '/return \(<<<HTML((.|\s)+)HTML\);/m';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        $result = !isset($matches[0]) ? '' : $matches[0][1];

        return $result;
    }
}
