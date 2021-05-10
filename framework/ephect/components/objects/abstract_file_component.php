<?php

namespace Ephect\Components;

use Ephect\Components\Generators\ChildrenParser;
use Ephect\Components\Generators\ComponentParser;
use Ephect\Components\Generators\ParserService;
use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

define('INCLUDE_PLACEHOLDER', "include_once CACHE_DIR . '%s';" . PHP_EOL);
define('USE_PLACEHOLDER', "use %s;" . PHP_EOL);
define('CHILDREN_PLACEHOLDER', "// \$children = null;" . PHP_EOL);

class AbstractFileComponent extends AbstractComponent implements FileComponentInterface
{

    protected $filename = '';

    public function __construct(?string $id = null, string $motherUID = '')
    {
        if ($id !== null) {
            ComponentRegistry::uncache();
            $this->class = ComponentRegistry::read($id);
            if($this->class !== null) {
                $this->filename = ComponentRegistry::read($this->class);
                $this->filename = $this->filename ?: '';

                $this->uid = ComponentRegistry::read($this->filename);
                $this->uid = $this->uid ?: '';

            }

            if($this->uid !== $this->id) {
                $this->function = $id;
            }
            if($this->uid === $this->id) {
                $this->function = self::functionName($this->class);
            }
        }

        $this->getUID();
        $this->motherUID = $motherUID ?: $this->uid;

    }

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

    public function load(?string $filename = null): bool
    {
        $result = false;
        $filename = $filename ?: '';

        $this->filename = ($filename !== '') ? $filename : $this->filename;

        if($this->filename === '') {
            return false;
        }

        $this->code = Utils::safeRead(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $this->filename);
        if ($this->code === null) {
            $this->code = Utils::safeRead(CACHE_DIR . $this->filename);
        }

        [$this->namespace, $this->function, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        $result = $this->code !== null;

        return  $result;
    }

    // public static function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array
    public function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array
    {
        [$fqFunctionName, $cacheFilename, $isCached] = $this->findComponent($functionName, $motherUID);
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
        /* TO BEGIN WITH */
        // CodeRegistry::uncache();
        // $class = $this->getFullyQualifiedFunction();
        // $item = CodeRegistry::read($class);
        /* TO BEGIN WITH */

        CodeRegistry::uncache();

        // $parser = new ChildrenParser($this);

        // $parser->doUncache();
        // $parser->doPhpTags();

        // $this->children = $parser->doChildrenDeclaration();
        // $parser->doValues();
        // $parser->doEchoes();
        // $parser->doArrays();
        // $parser->doUseEffect();
        // $parser->useVariables();
        // $parser->normalizeNamespace();
        // $parser->doFragments();
        // // $parser->doEntities();

        // $componentList = $parser->doComponents();
        // $html = $parser->getHtml();
        // $this->code = $html;
        // $this->updateFile();
        
        // $openComponentList = $parser->doOpenComponents();
        // $html = $parser->getHtml();
        // $this->code = $html;
        // $this->updateFile();

        // $this->componentList = array_unique(array_merge($componentList, $openComponentList));

        // $this->doIncludes();

        // $parser->doCache();

        $ps = new ParserService($this);
        $ps->parse();

        CodeRegistry::cache();


    }

    public function doIncludes(): void
    {
        ComponentRegistry::uncache();
        $motherUID = $this->motherUID;

        foreach ($this->componentList as $component) {
            [$fqFunctionName, $cacheFilename] = $this->renderComponent($motherUID, $component);

            $moduleNs = "namespace " . $this->getNamespace() . ';' . PHP_EOL;
            $include = str_replace('%s', $cacheFilename, INCLUDE_PLACEHOLDER);
            $this->code = str_replace($moduleNs, $moduleNs . PHP_EOL . $include, $this->code);

        }
    }

    // public static function render(string $functionName, ?array $functionArgs = null, string $motherUID = ''): void
    public function render(?array $functionArgs = null): void
    {
        [$fqFunctionName, $cacheFilename] = $this->renderComponent($this->motherUID, $this->function, $functionArgs);

        $html = $this->renderHTML($cacheFilename, $fqFunctionName, $functionArgs);
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

        $token = 'N' . str_replace('-', '', $motherUID);

        $cachedir = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOfUnique();
        $copyFile = $component->getFlattenSourceFilename();
        $html = $component->getCode();
        $ns = $component->getNamespace();
        $html = str_replace($ns , $ns . '\\' . $token, $html);

        if ($componentList === null) {
            // Utils::safeWrite($cachedir . $copyFile, $html);
            copy(CACHE_DIR . $copyFile, $cachedir . $copyFile);

            return $copyFile;
        }

        $fqFuncName = $component->getFullyQualifiedFunction();

        foreach ($componentList as $funcName => $fqFuncName) {

            $nextComponent = !isset($list[$fqFuncName]) ? null : $list[$fqFuncName];

            $nextCopyFile = '';
            if ($nextComponent !== null) {
                $nextCopyFile = $nextComponent->getFlattenSourceFilename();
            }

            if ($nextComponent === null) {
                $nextCopyFile = PluginRegistry::read($fqFuncName);
            }
            if (file_exists($cachedir . $nextCopyFile)) {
                continue;
            }

            if ($nextComponent !== null) {
                $component->copyComponents($list, $motherUID, $nextComponent);
            }
        }

        copy(CACHE_DIR . $copyFile, $cachedir . $copyFile);
        // Utils::safeWrite($cachedir . $copyFile, $html);

        return $copyFile;
    }


    public function updateFile():  void 
    {

        $cp = new ComponentParser($this);
        $struct = $cp->doDeclaration();
        $decl = $struct->toArray();
        $filename = $this->getFlattenSourceFilename();
        Utils::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $filename, $this->code);

        CodeRegistry::write($this->getFullyQualifiedFunction(), $decl);
        CodeRegistry::cache();
    }

    protected function cacheHtml(): ?string
    {
        $cache_file = static::getFlatFilename($this->filename);
        $result = Utils::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $cache_file, $this->code);

        // if(file_exists(CACHE_DIR . $cache_file)) {
        //     unlink(CACHE_DIR . $cache_file);
        // }

        $cache = (($cache = CacheRegistry::read($this->motherUID)) === null) ? [] : $cache;

        $cache[$this->getFullyQualifiedFunction()] = static::getFlatFilename($this->getSourceFilename());
        CacheRegistry::write($this->motherUID, $cache);
        CacheRegistry::cache();

        return $result === null ? $result : $cache_file;
    }
}
