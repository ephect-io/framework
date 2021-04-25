<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentEntity;
use Ephect\Components\ComponentEntityInterface;
use Ephect\Registry\CodeRegistry;

class ChildrenParser extends Parser
{
    /**
     * 
     * REGEX 101 https://regex101.com/r/BQRDmy/3 
     * 
     * @param null|string $subject 
     * @return null|object 
     */
    public function doChildrenDeclaration(?string $subject = null): ?object    {
        $result = null;
        $subject = $subject ?: $this->html;

        
        $re = '/(function([\w ]+)\(\$([\w]+)[^\)]*\)(\s|.)+?(\{))(\s|.)+?(\{\{ \3 \}\})/';
        //$re = '/(function([\w ]+)\(\$([\w]+)[^\)]*\))(\s|.)+?(\{\$\3\})/';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $functionDeclaration = $match[1];
            $componentName = $match[2];
            $variable = $match[7];

            $result = (object) ['declaration' => $functionDeclaration, 'component' => $componentName, 'variable'=>$variable];
        }

        return $result;
    }

    /**
     * 
     * @param string $tag 
     * @param null|string $subject 
     * @return array 
     */
    public function doOpenComponents(string $tag = '[A-Z][\w]+', ?string &$subject = null): array
    {
        $result = [];

        $re = '/<(' . $tag . ')(\b[^>]*)>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)(<\/\1>)/m';
        $subject = $subject ?: $this->html;

        preg_match_all($re, $subject, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

        // $entity = $this->component->getEntity();

        foreach ($matches as $match) {
            $component = $match[0][0];
            $componentName = $match[1][0];
            $componentArgs = empty(trim($match[2][0])) ? null : trim($match[2][0]);
            $componentBody = trim($match[3][0]);


            // $componentName = $child->getName();
            // $componentArgs = $child->props();
            // $args = Maker::doArgumentsToString($componentArgs);
            // $component = $child->getText();

            if ($componentArgs !== null) {
                $componentArgs = $this->doArguments($componentArgs);
            }

            if ($componentName !== 'Block' && $this->maker->makeChildren($component, $componentName, $componentArgs, $componentBody, $subject)) {
                array_push($result, $componentName);
            }
        }

        $this->html = $subject;

        return $result;
    }

    public function doEntities(): array
    {
        $result = [];

        $entity = $this->component->getEntity();

        if($entity === null) {
            return $result;
        }
        
        if(!$entity->hasChildren()) {
            $this->doClosedEntity($entity);
        }

        if($entity->hasChildren()) {
            $this->doOpenEntity($entity);
        }

        return $result;
    }

    protected function doClosedEntity(ComponentEntityInterface $entity): void
    {
        $name = $entity->getName();
        $args = $entity->props();
    }

    protected function doOpenEntity(ComponentEntity $entity): void
    {
        # code...
    }

    /**
     * 
     * @return array 
     */
    public function doComponents(): array
    {
        $result = [];

        /*
        * $re = '/<\/?([A-Z]\w+)((\s|.*?)+)>|<\/?>/m';
        */
        $re = '/<([A-Z][\w]*)([\w\s\{\}\(\)\'"=][^\>]*)((\s|[^\/\>].))?\/\>/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        $uid = $this->component->getMotherUID();
        $motherUID = '';
        if(file_exists(CACHE_DIR . $uid)) {
            $motherUID = $uid;
        }

        foreach ($matches as $match) {
            $component = $match[0];
            $componentName = $match[1];
            $componentArgs = isset($match[2]) ? $match[2] : '';

            $args = 'null';
            if (trim($componentArgs) !== '') {
                $componentArgs = $this->doArguments($componentArgs);
                $args = Maker::doArgumentsToString($componentArgs);
            }

            //$parent = $this->component->getFullyQualifiedFunction();

            $componentRender = "<?php \$$componentName = new \\Ephect\\Components\\Component('$componentName', '$motherUID'); ?>\n";
            $componentRender .= "\t\t\t<?php \$${componentName}->render($args); ?>\n";

            $this->html = str_replace($component, $componentRender, $this->html);

            array_push($result, $componentName);

        }
        
        return $result;
    }

    /**
     * May be obsolete
     * 
     * @param string $componentName 
     * @param array $componentArgs 
     * @param string $componentBody 
     * @return bool 
     */
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

    public function doFragments(): void
    {
        $this->html = str_replace('<>', '', $this->html);
        $this->html = str_replace('</>', '', $this->html);

    }
}
