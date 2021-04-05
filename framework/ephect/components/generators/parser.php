<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentInterface;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\FrameworkRegistry;

class Parser
{
    protected $html = '';
    protected $component = null;
    protected $useVariables = [];
    protected $parentHTML = '';
    protected $maker = null;

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->html = $comp->getCode();
        $this->parentHTML = $comp->getParentHTML();
        $this->maker = new Maker($comp);
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


    public function doValues(): bool
    {
        $result = null;

        $re = '/\{([a-zA-Z0-9_\-\>]*)\}/m';
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

            $this->html = str_replace('{' . $variable . '}', '$' . $variable . '', $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    public function doEchoes(): bool
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

            $uid = $this->component->getUID();

            if ($variable === 'children') {
                /**
                 * $this->html = str_replace('{{ children }}', "<?php \Ephect\Components\Component::bind('$uid'); ?>", $this->html);
                 */
                
                $html = CodeRegistry::read($uid);
                $html = urldecode($html);
         
                $this->html = str_replace('{{ children }}', $html, $this->html);

                continue;
            }

            $this->html = str_replace('{{ ' . $variable . ' }}', '<?php echo $' . $variable . '; ?>', $this->html);
            $this->html = str_replace('{' . $variable . '}', '$' . $variable . '', $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    public function doPhpTags(): bool
    {
        $this->html = str_replace('{?', '<?php ', $this->html);
        $this->html = str_replace('?}', '?> ', $this->html);

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

    /** TO BE DONE on bas of regex101 https://regex101.com/r/QZejMW/2/ */
    public function doFunctionArguments(string $subject): ?array
    {
        $result = [];
        $re = '/((function) ([\w]+)\()?([\,]?[\.]*\$[\w]*)/m';

        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        return $result;
    }

    public function doArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_]*)=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/m';

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
        $compNamespace = $this->component->getNamespace();

        $re = '/use ([A-Za-z0-9\\\\ ]*\\\\)?([A-Za-z0-9 ]*) as ([A-Za-z0-9 ]*);/m';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $componentNamespace = trim($match[1], '\\');
            $componentFunction = $match[2];
            $componentAlias = $match[3];

            $componentNamespace = ($componentNamespace === '') ? $compNamespace : $componentNamespace;
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
