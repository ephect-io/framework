<?php

namespace Ephect\Components;

use BadFunctionCallException;
use Ephect\ElementTrait;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\Tree;
use Exception;
use ReflectionFunction;
use tidy;

abstract class AbstractComponent extends Tree implements ComponentInterface
{
    use ElementTrait;

    protected $code;
    protected $parentHTML;
    protected $componentList = [];
    protected $children = null;
    protected $declaration = null;
    protected $entity = null;
    protected $bodyStartsAt = 0;

    public function getBodyStart(): int
    {
        return $this->bodyStartsAt;
    }

    public function getDeclaration(): ?ComponentDeclaration
    {
        if($this->declaration === null) {
            $this->setDeclaration();
        }

        return $this->declaration;
    }

    public function resetDeclaration(): void
    {
        $this->declaration = null;
    }

    protected function setDeclaration(): void
    {
        $fqName = ComponentRegistry::read($this->uid);

        if($fqName === null ) {
            $fqName = $this->getFullyQualifiedFunction();
            if($fqName === null ) {
                throw new Exception('Please the component is defined in the registry before asking for its entity');
            }
        }
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->getMotherUID());

        $list = CodeRegistry::read($fqName);
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        $this->declaration = $decl;
    }

    public function getEntity(): ?ComponentEntity
    {
        if($this->entity === null) {
            $this->setEntity();
        }

        return $this->entity;
    }

    protected function setEntity() {
        $decl = $this->getDeclaration();
        $this->entity = $decl->getComposition();
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

    public function findComponent(string $componentName, string $motherUID): array
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

    public function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null): string
    {
        include_once CACHE_DIR . $cacheFilename;

        $funcReflection = new ReflectionFunction($fqFunctionName);
        $funcParams = $funcReflection->getParameters();

        $html = '';
        if ($funcParams === []) {
            ob_start();
            $fn = call_user_func($fqFunctionName);
            $fn();
            $html = ob_get_clean();
        }

        if ($funcParams !== []) {

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

    protected function format(string $html): string
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
