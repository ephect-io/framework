<?php

namespace Ephect\Components;

use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

define('INCLUDE_PLACEHOLDER', "include_once CACHE_DIR . '%s';" . PHP_EOL);
define('CHILDREN_PLACEHOLDER', "// \$children = null;" . PHP_EOL);

class AbstractFileComponent extends AbstractComponent implements FileComponentInterface
{

    protected $filename = '';

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public function getFlattenSourceFilename(): string
    {
        // 'source_' . 
        $cache_file = static::getFlatFilename($this->filename);

        return $cache_file;
    }

    public function getFlattenFilename(): string
    {
        $cache_file = static::getFlatFilename($this->filename);

        return $cache_file;
    }

    public static function getFlatFilename(string $basename): string
    {
        $basename = pathinfo($basename, PATHINFO_BASENAME);

        $cache_file = str_replace('/', '_', $basename);

        return $cache_file;
    }

    public function load(string $filename): bool
    {
        $result = false;
        $this->filename = $filename;

        $this->code = Utils::safeRead(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $this->filename);
        if ($this->code === null) {
            $this->code = Utils::safeRead(CACHE_DIR . $this->filename);
        }

        list($this->namespace, $this->function) = ElementUtils::getFunctionDefinition($this->code);
        $result = $this->code !== null;

        return  $result;
    }

    public static function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array
    {
        [$fqFunctionName, $cacheFilename, $isCached] = static::findComponent($functionName, $motherUID);
        if (!$isCached) {
            ComponentRegistry::uncache();

            $fqName = ComponentRegistry::read($functionName);
            $component = ComponentFactory::create($fqName, $motherUID);
            $component->parse();

            $motherUID = $component->getMotherUID();

            $cacheFilename = $motherUID . DIRECTORY_SEPARATOR . $component->getFlattenFilename();
        }

        return [$fqFunctionName, $cacheFilename];
    }

    public function parse(): void
    {
        parent::parse();

        ComponentRegistry::uncache();

        if ($this->children !== null) {


            $statment = <<<PHP
            \tlist(\$props, \$children) = \Ephect\Components\AbstractComponent::passChidren(\$children);

            PHP;

            $declaration = $this->children->declaration . PHP_EOL;
            $this->code = str_replace($declaration, $declaration . $statment, $this->code);
        }

        foreach ($this->componentList as $component) {

            $motherUID = $this->motherUID;
            [$fqFunctionName, $cacheFilename] = self::renderComponent($motherUID, $component);

            $ns = "namespace " . $this->getNamespace() . ';' . PHP_EOL;
            if (false === strpos($this->code, $ns)) {
                $namespace = ElementUtils::getNamespaceFromFQClassName($fqFunctionName);
                $ns = "namespace " . $namespace . ';' . PHP_EOL;
            }

            $include = str_replace('%s', $cacheFilename, INCLUDE_PLACEHOLDER);

            $this->code = str_replace($ns, $ns . PHP_EOL . $include, $this->code);
        }

    }

    public static function render(string $functionName, ?array $functionArgs = null, string $motherUID = ''): void
    {
        [$fqFunctionName, $cacheFilename] = self::renderComponent($motherUID, $functionName, $functionArgs);

        $html = parent::renderHTML($cacheFilename, $fqFunctionName, $functionArgs);
        echo $html;
    }

    public function copyComponents(array &$list, ?string $motherUID = null, ?ComponentInterface $component = null): ?string
    {
        if ($component === null) {
            $component = $this;
        }

        if ($motherUID === null) {
            $motherUID = $component->getUID();
            mkdir(CACHE_DIR . $motherUID, 0775);
        }

        $cachedir = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOf();
        $copyFile = $component->getFlattenSourceFilename();

        if ($componentList === null) {
            copy(CACHE_DIR . $copyFile, $cachedir . $copyFile);

            return $copyFile;
        }

        $fqFuncName = $component->getFullyQualifiedFunction();

        foreach ($componentList as $funcName => $fqFuncName) {

            $nextComponent = !isset($list[$fqFuncName]) ? null : $list[$fqFuncName];

            $nextCopyFile = '';
            if($nextComponent !== null) {
                $nextCopyFile = $nextComponent->getFlattenSourceFilename();
            }

            if($nextComponent === null) {
                $nextCopyFile = PluginRegistry::read($fqFuncName);
            }
            if (file_exists($cachedir . $nextCopyFile)) {
                continue;
            }

            if($nextComponent !== null) {
                $component->copyComponents($list, $motherUID, $nextComponent);
            }
        }

        // Utils::safeWrite($cachedir . $copyFile, $subject);
        copy(CACHE_DIR . $copyFile, $cachedir . $copyFile);

        return $copyFile;
    }

    protected function cacheHtml(): ?string
    {
        $cache_file = static::getFlatFilename($this->filename);
        $result = Utils::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $cache_file, $this->code);
        
        if(file_exists(CACHE_DIR . $cache_file)) {
            unlink(CACHE_DIR . $cache_file);
        }

        $cache = (($cache = CacheRegistry::read($this->motherUID)) === null) ? [] : $cache;

        $cache[$this->getFullyQualifiedFunction()] = static::getFlatFilename($this->getSourceFilename());
        CacheRegistry::write($this->motherUID, $cache);
        CacheRegistry::cache();

        return $result === null ? $result : $cache_file;
    }
}
