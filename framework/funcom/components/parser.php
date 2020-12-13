<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;
use stdClass;

class Parser
{
    private $html = '';
    private $view = null;
    private $useVariables = [];
    private $parentHTML = '';

    public function __construct(ComponentInterface $view)
    {
        $this->view = $view;
        $this->html = $view->getCode();
        $this->parentHTML = $view->getParentHTML();
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

            $this->html = str_replace('{{ ' . $variable . ' }}', '<?php echo $' . $variable . ' ?>', $this->html);
        }

        $result = $this->html !== null;

        return $result;
    }

    public function doArrays(): bool
    {
        $result = null;

        $re = '/\{\{ \.\.\.([a-z0-9_\-\>]*) \}\}/m';
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

            $this->html = str_replace('{{ ...' . $variable . ' }}', '<?php echo $' . $variable . ' ?>', $this->html);
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

        $re = '/ <([A-Z][\w]*)([\S\{\}\(\)\'"= ][^\>]*)((\s|[^\/\>].))?\/\>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);


        foreach ($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? $match[2] : '';

            $args = '';
            if (trim($componentArgs) !== '') {
                $componentArgs = $this->doArguments($componentArgs);
                $args = ', ' . $this->doArgumentsToString($componentArgs);
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
    public function doOpenComponents(string $tag = '[A-Z][\w]+', ?string &$subject = null): bool
    {
        $result = '';

        $re = '/<(' . $tag . ')(\b[^>]*)>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)(<\/\1>)/m';
        $subject = $subject ?: $this->html;

        preg_match_all($re, $subject, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);


        foreach ($matches as $match) {
            $component = $match[0][0];
            $componentName = $match[1][0];
            $componentArgs = isset($match[2][0]) ? trim($match[2][0]) : null;
            $componentBody = trim($match[3][0]);

            $componentBoundaries = '["opener" => "' . urlencode(substr($component, 0, $match[3][1] - $match[0][1])) . '", ';
            $componentBoundaries .= '"closer" => "' . urlencode($match[4][0]) . '", ]';

            if (trim($componentArgs) !== null) {
                $componentArgs = $this->doArguments($componentArgs);
            }

            if (empty($componentBody)) {
                continue;
            }

            if ($componentName === 'Block') {
                $this->doOpenComponent($componentName, $componentArgs, $componentBody);
                continue;
            }

            $this->doFragment($component, $componentName, $componentArgs, $componentBody, $componentBoundaries, $subject);
        }

        $this->html = $subject;

        $result = $this->html !== null;

        return $result;
    }

    public function doChildren(string $tag = '[A-Z][\w]+', ?string $subject = null): ?array
    {
        $result = [];

        $re = '/<(' . $tag . ')(\b[^>]*)>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)<\/\1>/m';

        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? trim($match[2]) : null;
            $componentBody = trim($match[3]);

            if (trim($componentArgs) === null) {
                return null;
            }

            $componentArgs = $this->doChildrenArguments($componentArgs);

            $key = key_exists('name', $componentArgs) ? $componentArgs['name'] : null;

            if ($key === null) {
                return null;
            }

            $result[$key] = (object) [
                "component" => $component,
                "args" => $componentArgs,
                "body" => $componentBody,
            ];
        }

        return $result;
    }


    public function doChildrenArguments(string $componentArgs): ?array
    {
        $result = [];

        $re = '/([A-Za-z0-9_]*)=("([\S\\\\\" ]*)"|\'([\S\\\\\' ]*)\'|\{([\S\\\\\{\}\(\)=\<\> ]*)\})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[2], 1), 0, -1);

            $result[$key] = $value;
        }

        $result = count($result) === 0 ? null : $result;

        return $result;
    }

    public function doFragment(string $component, string $componentName, ?array $componentArgs, string $componentBody, string $componentBoundaries, ?string &$subject): bool
    {
        $uid = uniqid(time(), true);

        $args = $this->doArgumentsToString($componentArgs);
        $args = ', ' . (($args === null) ? "null" : $args);
        $body = urlencode($componentBody);

        CodeRegistry::write($uid, $body);

        $className = $this->view->getFunction();
        $classArgs = 'null';
        
        $componentRender = "<?php \FunCom\Components\View::make('$className', $classArgs, '$componentName'$args, $componentBoundaries, '$uid'); ?>";
        
        //$componentRender = $this->makeFragment($componentName, $componentArgs, $uid);

        $subject = str_replace($component, $componentRender, $subject);

        $result = $subject !== null;

        return $result;
    }


    public function makeFragment(string $componentName, ?array $componentArgs, string $uid): string
    {
        list($className, $filename, $isCached) = View::findComponent($componentName);

        $html = Utils::safeRead(($isCached ? CACHE_DIR : SRC_ROOT) . $filename);
        // $html = View::renderHTML($componentName, $componentArgs);

        $fragment = new Fragment($uid, $html);

        $fragment->parse();

        $html = $fragment->getParentHTML();


        $functionName = $this->view->getFunction();

        list($className, $filename, $isCached) = View::findComponent($functionName);

        $prehtml = new PreHtml($html);
        $prehtml->load($filename);
        $prehtml->parse();

        $html = $prehtml->getCode();

        Utils::safeWrite(CACHE_DIR . $filename, $html);

        return $html;
    }

    public function doMake(): void
    {
        $re = '/<\?php \\\\FunCom\\\\Components\\\\View::make\(\'.*\'\); \?>/m';
        $subject = $this->html;

        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $makeStatement = $match[0];
            $subject = str_replace($makeStatement, $this->parentHTML, $subject);
        }
        $this->html = $subject;
    }

    public function doOpenComponent(string $componentName, array $componentArgs, string $componentBody): bool
    {

        $componentArgs = $this->doArgumentsToString($componentArgs);

        $result = false;
        $re = '/<(' . $componentName . ')(' . $componentArgs . ')>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)<\/\1>/m';

        $str = $this->parentHTML;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $parentComponent = $match[0];
            $this->parentHTML = str_replace($parentComponent, $componentBody, $this->parentHTML);
        }

        $result = $this->parentHTML !== null;

        return $result;
    }

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

    public function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {

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
