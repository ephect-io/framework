<?php

namespace FunCom\Components\Generators;

class ChildrenParser extends Parser
{
   /**
     * UNDER CONSTRUCTION
     */
    public function doOpenComponents(string $tag = '[A-Z][\w]+', ?string &$subject = null): array
    {
        $result = [];

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

            // $previousBody = $componentBody;
            // if($this->doBlocks($componentName, $componentBody)) 
            // {
            //     $subject = str_replace($previousBody, $componentBody, $subject);
            //     $component = str_replace($previousBody, $componentBody, $component);
            // }

            if ($componentName === 'Block') {
                $this->doOpenComponent($componentName, $componentArgs, $componentBody);
                continue;
            }

            if ($this->maker->makeChildren($component, $componentName, $componentArgs, $componentBody, $componentBoundaries, $subject)) {
                array_push($result, $componentName);
            }
        }

        $this->html = $subject;

        return $result;
    }

    /** REGEX 101 https://regex101.com/r/BQRDmy/3 */
    public function doChildrenDeclaration(?string $subject = null): ?object
    {
        $result = null;
        $subject = $subject ?: $this->html;

        
        $re = '/(function([\w ]+)\(\$([\w]+)[^\)]*\)(\s|.)+?(\{))(\s|.)+?(\{\{ \3 \}\})/';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $functionDeclaration = $match[1];
            $componentName = $match[2];
            $variable = $match[7];

            $result = (object) ['declaration' => $functionDeclaration, 'component' => $componentName, 'variable'=>$variable];
        }

        return $result;
    }

    public function doOpenComponent(string $componentName, array $componentArgs, string $componentBody): bool
    {

        $componentArgs = Maker::doArgumentsToString($componentArgs);

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

}
