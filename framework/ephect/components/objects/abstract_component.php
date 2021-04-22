<?php

namespace Ephect\Components;

use BadFunctionCallException;
use Ephect\Components\Generators\ChildrenParser;
use Ephect\Components\Generators\Parser;
use Ephect\ElementTrait;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\Tree;
use Exception;
use tidy;

abstract class AbstractComponent extends Tree implements ComponentInterface
{
    use ElementTrait;

    // protected $function = null;
    protected $code;
    protected $parentHTML;
    protected $componentList = [];
    protected $children = null;
    protected $entity = null;
    protected $bodyStartsAt = 0;

    public function getBodyStart(): int
    {
        return $this->bodyStartsAt;
    }

    public function getEntity(): ?ComponentEntity
    {
        if($this->entity === null) {
            $this->setEntity();
        }

        return $this->entity;
    }

    protected function setEntity() {
        $fqName = ComponentRegistry::read($this->uid);

        if($fqName === null ) {
            $fqName = $this->getFullyQualifiedFunction();
            if($fqName === null ) {
                throw new Exception('Please the component is defined in the registry before asking for its entity');
            }
        }

        $list = CodeRegistry::read($fqName);
        $this->entity = ComponentEntity::buildFromArray($list);
    }

    public function getParentHTML(): ?string
    {
        return $this->parentHTML;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getFullyQualifiedFunction(): ?string
    {
        if($this->function === null) return null;
        return $this->namespace  . '\\' . $this->function;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function analyse(): void
    {
        $parser = new Parser($this);
        $parser->doUses();
        $parser->doUsesAs();
    }

    public function compose(): void
    {
        $composition = CodeRegistry::read($this->getFullyQualifiedFunction());
        $entity = ComponentEntity::buildFromArray($composition);
        $this->add($entity);
    }

    public function composedOf(): ?array
    {
        $names = [];

        $this->forEach(function (ComponentEntityInterface $item, $key) use (&$names) {
            $funcName = $item->getName();
            $fqFuncName = ComponentRegistry::read($funcName);
            $names[$funcName] = $fqFuncName;
        }, $this);

        $names = array_filter($names, function ($item) {
            return $item !== null;
        });

        if (count($names) === 0) {
            $names = null;
        }

        return $names;
    }

    public function composedOfUnique():?array
    {
        $result = $this->composedOf();

        if($result === null) return null;

        $result = array_unique($result);

        return $result;
    }

    public function parse(): void
    {
        /* TO BEGIN WITH */
        // CodeRegistry::uncache();
        // $class = $this->getFullyQualifiedFunction();
        // $item = CodeRegistry::read($class);
        /* TO BEGIN WITH */

        $parser = new ChildrenParser($this);

        $parser->doUncache();
        $parser->doPhpTags();

        $this->children = $parser->doChildrenDeclaration();
        $parser->doValues();
        $parser->doEchoes();
        $parser->doArrays();
        $parser->doUseEffect();
        $parser->useVariables();
        $parser->normalizeNamespace();
        $parser->doFragments();
        $parser->doEntities();
        $componentList = $parser->doComponents();
        $openComponentList = $parser->doOpenComponents();

        $this->componentList = array_unique(array_merge($componentList, $openComponentList));

        $html = $parser->getHtml();

        $parser->doCache();

        $this->code = $html;
    }

    public static function findComponent(string $componentName, string $motherUID): array
    {
        ComponentRegistry::uncache();
        $uses = ComponentRegistry::items();
        $fqFuncName = isset($uses[$componentName]) ? $uses[$componentName] : null;

        if ($fqFuncName === null) {
            throw new BadFunctionCallException('The component ' . $componentName . ' does not exist.');
        }

        CacheRegistry::uncache();

        if ($motherUID === '') {
            $filename = $uses[$fqFuncName];
            $motherUID = $uses[$filename];
        }
        $filename = CacheRegistry::read($motherUID, $fqFuncName);
        $filename = ($filename !== null) ? $motherUID . DIRECTORY_SEPARATOR . $filename : $filename;
        $isCached = $filename !== null;

        return [$fqFuncName, $filename, $isCached];
    }

    public static function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null): string
    {
        include_once CACHE_DIR . $cacheFilename;

        // $flatComp = CodeRegistry::read($fqFunctionName);
        // $comp = ComponentEntity::buildFromArray($flatComp);
        // $functionArgs = ($comp !== null) ? $comp->props() : $functionArgs;

        $html = '';
        if ($functionArgs === null) {
            ob_start();
            $fn = call_user_func($fqFunctionName);
            $fn();
            $html = ob_get_clean();
        }

        if ($functionArgs !== null) {

            $json = json_encode($functionArgs);
            $props = json_decode($json);
            foreach ($props as $key => $value) {
                $props->{$key} = urldecode($value);
            }
            ob_start();
            $fn = call_user_func($fqFunctionName, $props);
            $fn();
            $html = ob_get_clean();
        }

        // $fqFunctionName = explode('\\', $functionName);
        // $function = array_pop($fqFunctionName);
        // if ($function === 'App') {
        //     $html = self::format($html);
        // }

        return $html;
    }

    public static function format(string $html): string
    {
        $config = [
            'indent'      => true,
            'output-html' => true,
            'wrap'        => 200
        ];

        $tidy = new tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy->value;
    }
}
