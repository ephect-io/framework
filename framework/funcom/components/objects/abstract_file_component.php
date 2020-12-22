<?php

namespace FunCom\Components;

use FunCom\IO\Utils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

define('INCLUDE_PLACEHOLDER', "include CACHE_DIR . '%s';" . PHP_EOL);
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

        list($this->namespace, $this->function) = $this->getFunctionDefinition();
        $result = $this->code !== null;

        return  $result;
    }

    public static function renderComponent(string $functionName, ?array $functionArgs = null): string
    {
        if(!static::checkCache($functionName)) {
            ClassRegistry::uncache();
            ViewRegistry::uncache();

            $fqName = UseRegistry::read($functionName);
            $filename = ClassRegistry::read($fqName);
            $uid = ViewRegistry::read($filename);
            $view = new View($uid);
            $view->load($filename);
            $view->parse();

            $html = $view->getCode();
            
            CacheRegistry::write($view->getFullCleasName(), static::getCacheFilename($view->getSourceFilename()));
            CacheRegistry::cache();

            return $html;
        }

        $fqName = UseRegistry::read($functionName);
        $filename = CacheRegistry::read($fqName);

        $html = Utils::safeRead(CACHE_DIR . $filename);

        return $html;
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

            self::renderComponent($component);


            $fqName = UseRegistry::read($component);
            $filename = CacheRegistry::read($fqName);

            $ns = "namespace " . $this->getNamespace() . ';' . PHP_EOL;

            $include = str_replace('%s', $filename, INCLUDE_PLACEHOLDER);

            $this->code = str_replace($ns, $ns . PHP_EOL . $include, $this->code);

        }
    }
    
  
}
