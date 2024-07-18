<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\Generators\ParserService;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Registry\CacheRegistry;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\Web\ApplicationIgniter;
use Ephect\Framework\Web\Request;
use Exception;
use ReflectionException;

define('INCLUDE_PLACEHOLDER', "include_once CACHE_DIR . '%s';");
define('USE_PLACEHOLDER', "use %s;" . PHP_EOL);

abstract class AbstractFileComponent extends AbstractComponent implements FileComponentInterface
{

    protected ?string $filename = '';

    public function __construct(?string $id = null, string $motherUID = '')
    {
        $this->id = $id ?: '';
        if ($id === null) {
            $this->getUID();
            $this->motherUID = $motherUID ?: $this->uid;

            return;
        }

        ComponentRegistry::load();
        $this->class = ComponentRegistry::read($id);
        if ($this->class !== null) {
            $this->filename = ComponentRegistry::read($this->class);
            $this->filename = $this->filename ?: '';

            $this->uid = ComponentRegistry::read($this->filename);
            $this->uid = $this->uid ?: '';
        } else {
            $this->class = WebComponentRegistry::read($id);
            if ($this->class !== null) {
                $this->filename = WebComponentRegistry::read($this->class);
                $this->filename = $this->filename ?: '';

                $this->uid = WebComponentRegistry::read($this->filename);
                $this->uid = $this->uid ?: '';
            }
        }

        if ($this->uid !== $this->id) {
            $this->function = $id;
        }
        if ($this->uid === $this->id) {
            $this->function = self::functionName($this->class);
        }

        $this->motherUID = $motherUID ?: $this->uid;

    }

    public static function createByHtml(string $html): static
    {
        $new = new static;
        $new->code = $html;

        return $new;
    }

    public function analyse(): void
    {
        $parser = new ParserService;
        $parser->doUses($this);
        $parser->doUsesAs($this);
    }

    /**
     * @throws ReflectionException
     */
    public function render(?array $functionArgs = null, ?Request $request = null): void
    {
        if($this->motherUID == $this->uid && $this->id !== 'App') {
            StateRegistry::loadByMotherUid($this->motherUID, true);
            $stateIgniter = new ApplicationIgniter;
            $stateIgniter->ignite();

        }

        [$fqFunctionName, $cacheFilename] = $this->renderComponent($this->motherUID, $this->function, $functionArgs);

        echo $this->renderHTML($cacheFilename, $fqFunctionName, $functionArgs, $request);
    }

    public function renderComponent(string $motherUID, string $functionName, ?array $functionArgs = null): array
    {
        [$fqFunctionName, $cacheFilename, $isCached] = $this->findComponent($functionName, $motherUID);
        if (!$isCached) {
            ComponentRegistry::load();
            WebComponentRegistry::load();

            $fqName = ComponentRegistry::read($functionName);
            $component = ComponentFactory::create($fqName, $motherUID);
            $component->parse();

            $motherUID = $component->getMotherUID();

//            $cacheFilename = $motherUID . DIRECTORY_SEPARATOR . $component->getFlattenFilename();
            $cacheFilename = $motherUID . DIRECTORY_SEPARATOR . $component->getSourceFilename();
        }

        return [$fqFunctionName, $cacheFilename];
    }

    /**
     * @throws Exception
     */
    public function parse(): void
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->getMotherUID());
        CodeRegistry::load();
        WebComponentRegistry::load();

        $parser = new ParserService();

        $parser->doUses($this);
        $parser->doUsesAs($this);

        $parser->doHeredoc($this);
        $this->code = $parser->getHtml();

        $parser->doInlineCode($this);
        $this->code = $parser->getHtml();

        $parser->doChildrenDeclaration($this);
        $this->children = $parser->getChildren();

        $parser->doArrays($this);
        $this->code = $parser->getHtml();

        $parser->doUseEffect($this);
        $this->code = $parser->getHtml();

        $parser->doReturnType($this);
        $this->code = $parser->getHtml();

        $parser->doWebComponent($this);

        $parser->doUseVariables($this);
        $this->code = $parser->getHtml();

        $parser->doNamespace($this);
        $this->code = $parser->getHtml();

        $parser->doFragments($this);
        $this->code = $parser->getHtml();
//        $filename = $this->getFlattenSourceFilename();
        $filename = $this->getSourceFilename();
        File::safeWrite(CACHE_DIR . $this->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $this->code);
        $this->updateComponent($this);

        $parser->doChildSlots($this);
        $this->code = $parser->getHtml();
        $this->updateComponent($this);

        while ($compz = $this->getDeclaration()->getComposition() !== null) {
            $parser->doOpenComponents($this);
            $this->code = $parser->getHtml();
            $this->updateComponent($this);

            $parser->doClosedComponents($this);
            $this->code = $parser->getHtml();
            $this->updateComponent($this);

            $parser->doIncludes($this);
            $this->code = $parser->getHtml();
        }

        CodeRegistry::save();
    }

    public function getFlattenSourceFilename(): string
    {
        return static::getFlatFilename($this->filename);
    }

    public static function getFlatFilename(string $basename): string
    {
//        $basename = pathinfo($basename, PATHINFO_BASENAME);

        return str_replace('/', '_', $basename);
    }

    public static function updateComponent(FileComponentInterface $component): string
    {
        $uid = $component->getUID();
        $motherUID = $component->getMotherUID();
//        $filename = $component->getFlattenSourceFilename();
        $filename = $component->getSourceFilename();

        $comp = new Component($uid, $motherUID);
        $comp->load($filename);
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        CodeRegistry::save();

        return $filename;
    }

    public function load(?string $filename = null): bool
    {
        $filename = $filename ?: '';

        $this->filename = ($filename !== '') ? $filename : $this->filename;

        if ($this->filename === '') {
            return false;
        }

        $this->code = File::safeRead(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $this->filename);
        if ($this->code === null) {
            $this->code = File::safeRead(COPY_DIR . $this->filename);
        }

        [$this->namespace, $this->function, $parameters, $returnedType, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        if ($this->bodyStartsAt == -1) {
            $this->makeComponent($this->filename, $this->code);
            [$this->namespace, $this->function, $parameters, $returnedType, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        }
        return $this->code !== null;
    }

    public abstract function makeComponent(string $filename, string &$html): void;

    public function getFlattenFilename(): string
    {
        return static::getFlatFilename($this->filename);
    }

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public function copyComponents(array &$list, ?string $motherUID = null, ?ComponentInterface $component = null): ?string
    {
        if ($component === null) {
            $component = $this;
            $motherUID = $component->getUID();
            if (!file_exists(CACHE_DIR . $motherUID)) {
                mkdir(CACHE_DIR . $motherUID, 0775);

                $flatFilename = CodeRegistry::getFlatFilename() . '.json';
                copy(CACHE_DIR . $flatFilename, CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $flatFilename);
            }
        }

        $cachedir = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOf();
//        $copyFile = $component->getFlattenSourceFilename();
        $copyFile = $component->getSourceFilename();
        $copyPath = pathinfo($copyFile, PATHINFO_DIRNAME);

        File::safeMkDir($cachedir . $copyPath);

        if ($componentList === null) {
            if (!file_exists($cachedir . $copyFile)) {
                copy(COPY_DIR . $copyFile, $cachedir . $copyFile);
            }

            return $copyFile;
        }

        $fqFuncName = $component->getFullyQualifiedFunction();
        foreach ($componentList as $entity) {

            $funcName = $entity->getName();
            $fqFuncName = ComponentRegistry::read($funcName);

            if ($fqFuncName === null) {
                continue;
            }
            $nextComponent = !isset($list[$fqFuncName]) ? null : $list[$fqFuncName];

            $nextCopyFile = '';
            if ($nextComponent !== null) {
                $nextCopyFile = $nextComponent->getSourceFilename();
            }

            if ($nextComponent === null) {
                $nextCopyFile = PluginRegistry::read($fqFuncName);
            }
            if (file_exists($cachedir . $nextCopyFile)) {
                continue;
            }

            if ($nextComponent === null) {
                continue;
            }
            $component->copyComponents($list, $motherUID, $nextComponent);
        }

        if (!file_exists($cachedir . $copyFile)) {
            copy(COPY_DIR . $copyFile, $cachedir . $copyFile);
        }

        return $copyFile;
    }

    public function updateFile(): void
    {
        $cp = new ComponentParser($this);
        $struct = $cp->doDeclaration();
        $decl = $struct->toArray();
//        $filename = $this->getFlattenSourceFilename();
        $filename = $this->getSourceFilename();
        File::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $filename, $this->code);

        CodeRegistry::write($this->getFullyQualifiedFunction(), $decl);
        CodeRegistry::save();
    }

    protected function cacheHtml(): ?string
    {
        return  $this->cacheFile(CACHE_DIR);
    }

    protected function cacheJavascript(): ?string
    {
        return  $this->cacheFile(RUNTIME_JS_DIR);
    }

    private function cacheFile($cacheDir): ?string
    {
//        $cache_file = static::getFlatFilename($this->filename);
        $cache_file = $this->getSourceFilename();
        $result = File::safeWrite($cacheDir . $this->motherUID . DIRECTORY_SEPARATOR . $cache_file, $this->code);

        $cache = (($cache = CacheRegistry::read($this->motherUID)) === null) ? [] : $cache;

//        $cache[$this->getFullyQualifiedFunction()] = static::getFlatFilename($this->getSourceFilename());
        $cache[$this->getFullyQualifiedFunction()] = $this->getSourceFilename();
        CacheRegistry::write($this->motherUID, $cache);
        CacheRegistry::save();

        return $result === null ? $result : $cache_file;
    }
}
