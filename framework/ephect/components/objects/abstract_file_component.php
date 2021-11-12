<?php

namespace Ephect\Components;

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

class AbstractFileComponent extends AbstractComponent implements FileComponentInterface
{

    protected $filename = '';

    public function __construct(?string $id = null, string $motherUID = '')
    {
        if ($id !== null) {
            ComponentRegistry::uncache();
            $this->class = ComponentRegistry::read($id);
            if ($this->class !== null) {
                $this->filename = ComponentRegistry::read($this->class);
                $this->filename = $this->filename ?: '';

                $this->uid = ComponentRegistry::read($this->filename);
                $this->uid = $this->uid ?: '';
            }

            if ($this->uid !== $this->id) {
                $this->function = $id;
            }
            if ($this->uid === $this->id) {
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

        if ($this->filename === '') {
            return false;
        }

        $this->code = Utils::safeRead(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $this->filename);
        if ($this->code === null) {
            $this->code = Utils::safeRead(COPY_DIR . $this->filename);
        }

        [$this->namespace, $this->function, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        $result = $this->code !== null;

        return  $result;
    }

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

    public function analyse(): void
    {
        $parser = new ParserService;
        $parser->doUses($this);
        $parser->doUsesAs($this);
    }

    public function parse(): void
    {
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->getMotherUID());
        CodeRegistry::uncache();

        $parser = new ParserService();

        $parser->doUses($this);
        $parser->doUsesAs($this);

        $parser->doMotherSlots($this);
        $res = $parser->getResult();
        $didMotherSlots = $res !== "" && $res !== null;
        $this->code = $parser->getHtml();

        $parser->doPhpTags($this);
        $this->code = $parser->getHtml();

        $parser->doChildrenDeclaration($this);
        $this->children = $parser->getChildren();

        $parser->doValues($this);
        $this->code = $parser->getHtml();

        $parser->doEchoes($this);
        $this->code = $parser->getHtml();

        $parser->doArrays($this);
        $this->code = $parser->getHtml();

        $parser->doUseEffect($this);
        $this->code = $parser->getHtml();

        $parser->doUseVariables($this);
        $this->code = $parser->getHtml();

        $parser->doNamespace($this);
        $this->code = $parser->getHtml();

        $parser->doFragments($this);
        $this->code = $parser->getHtml();
        $filename = $this->getFlattenSourceFilename();
        Utils::safeWrite(CACHE_DIR . $this->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $this->code);
        $this->updateComponent($this);

        if (!$didMotherSlots) {
            $parser->doChildSlots($this);
            $this->code = $parser->getHtml();
            $this->updateComponent($this);
        }

        while ($compz = $this->getDeclaration()->getComposition() !== null) {
            $parser->doClosedComponents($this);
            $this->code = $parser->getHtml();
            $this->updateComponent($this);

            $parser->doOpenComponents($this);
            $this->code = $parser->getHtml();
            $this->updateComponent($this);

            $parser->doIncludes($this);
            $this->code = $parser->getHtml();
        }

        CodeRegistry::cache();
    }

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
            if (!file_exists(CACHE_DIR . $motherUID)) {
                mkdir(CACHE_DIR . $motherUID, 0775);

                $flatFilename = CodeRegistry::getFlatFilename();
                copy(CACHE_DIR . $flatFilename, CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR . $flatFilename);
            }
        }

        $token = 'N' . str_replace('-', '', $motherUID);

        $cachedir = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOfUnique();
        $copyFile = $component->getFlattenSourceFilename();
        $html = $component->getCode();
        $ns = $component->getNamespace();
        $html = str_replace($ns, $ns . '\\' . $token, $html);

        if ($componentList === null) {
            if (!file_exists($cachedir . $copyFile)) {
                copy(COPY_DIR . $copyFile, $cachedir . $copyFile);
            }

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
        $filename = $this->getFlattenSourceFilename();
        Utils::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $filename, $this->code);

        CodeRegistry::write($this->getFullyQualifiedFunction(), $decl);
        CodeRegistry::cache();
    }

    public static function updateComponent(FileComponentInterface $component): string
    {
        $uid = $component->getUID();
        $motherUID = $component->getMotherUID();
        $filename = $component->getFlattenSourceFilename();

        $comp = new Component($uid, $motherUID);
        $comp->load($filename);
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration();
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        CodeRegistry::cache();

        return $filename;
    }

    protected function cacheHtml(): ?string
    {
        $cache_file = static::getFlatFilename($this->filename);
        $result = Utils::safeWrite(CACHE_DIR . $this->motherUID . DIRECTORY_SEPARATOR . $cache_file, $this->code);

        $cache = (($cache = CacheRegistry::read($this->motherUID)) === null) ? [] : $cache;

        $cache[$this->getFullyQualifiedFunction()] = static::getFlatFilename($this->getSourceFilename());
        CacheRegistry::write($this->motherUID, $cache);
        CacheRegistry::cache();

        return $result === null ? $result : $cache_file;
    }
}
