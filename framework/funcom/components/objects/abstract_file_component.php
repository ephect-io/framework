<?php

namespace FunCom\Components;

use FunCom\ElementUtils;
use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

define('INCLUDE_PLACEHOLDER', "include_once CACHE_DIR . '%s';" . PHP_EOL);
define('CHILDREN_PLACEHOLDER', "// \$children = null;" . PHP_EOL);

class AbstractFileComponent  extends AbstractComponent implements FileComponentInterface
{

    protected $filename = '';

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public static function getCacheFilename(string $basename): string
    {
        $cache_file = str_replace('/', '_', $basename);

        return $cache_file;
    }
    
    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(CACHE_DIR . $this->filename);
        if($this->code === null) {
            $this->code = Utils::safeRead(SRC_ROOT . $this->filename);
        }

        list($this->namespace, $this->function) = ElementUtils::getFunctionDefinition($this->code);
        $result = $this->code !== null;

        return  $result;
    }

    public static function renderComponent(string $functionName, ?array $functionArgs = null): array
    {
        if(!static::checkCache($functionName)) {
            ClassRegistry::uncache();
            ViewRegistry::uncache();

            $fqName = UseRegistry::read($functionName);
            $component = ComponentFactory::create($fqName);
            $component->parse();

            $html = $component->getCode();
            $namespace = $component->getNamespace();
            $functionName = $component->getFunction();
            
            CacheRegistry::write($component->getFullyQualifiedFunction(), static::getCacheFilename($component->getSourceFilename()));
            CacheRegistry::cache();

            return [$namespace, $functionName, $html];
        }

        $fqName = UseRegistry::read($functionName);
        $filename = CacheRegistry::read($fqName);
        $namespace = ElementUtils::getNamespaceFromFQClassName($fqName);

        $html = Utils::safeRead(CACHE_DIR . $filename);

        return [$namespace, $functionName, $html];
    }

    public function parse(): void
    {
        parent::parse();

        ClassRegistry::uncache();

        if($this->children !== null) {


            $statment = <<<PHP
            \tlist(\$props, \$children) = \FunCom\Components\AbstractComponent::passChidren(\$children);

            PHP;
            
            $declaration = $this->children->declaration . PHP_EOL;
            $this->code = str_replace($declaration, $declaration . $statment, $this->code);
            
        }

        foreach($this->componentList as $component) {

            list($namespace, $function, $html) = self::renderComponent($component);

            $fqName = UseRegistry::read($component);
            $filename = CacheRegistry::read($fqName);


            $ns = "namespace " . $this->getNamespace() . ';' . PHP_EOL;
            if(false === strpos($this->code, $ns)) {
                $ns = "namespace " . $namespace . ';' . PHP_EOL;
            }
            // $html = $this->code;

            $include = str_replace('%s', $filename, INCLUDE_PLACEHOLDER);

            $this->code = str_replace($ns, $ns . PHP_EOL . $include, $this->code);

        }
    }
    
  
}
