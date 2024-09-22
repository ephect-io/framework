<?php

namespace Forms\Application;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Tree\Tree;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Web\ApplicationIgniter;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentDeclarationStructure;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Components\ComponentFactory;
use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\CacheRegistry;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\WebApp\Web\Request;
use Exception;
use Forms\Generators\ParserService;
use ReflectionException;

define('INCLUDE_PLACEHOLDER', "include_once CACHE_DIR . '%s';");
define('USE_PLACEHOLDER', "use %s;" . PHP_EOL);

abstract class ApplicationComponent extends Tree implements FileComponentInterface
{
    use ElementTrait;
    use ComponentCodeTrait;

    protected ?ComponentDeclaration $declaration = null;
    protected ?ComponentEntity $entity = null;

    public function __construct(?string $id = null, string $motherUID = '')
    {
        parent::__construct([]);

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
        }

        if ($this->uid !== $this->id) {
            $this->function = $id;
        }
        if ($this->uid === $this->id) {
            $this->function = self::functionName($this->class);
        }

        $this->motherUID = $motherUID ?: $this->uid;

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
        if ($this->bodyStartsAt == -1 && !empty($this->code)) {
            $this->makeComponent($this->filename, $this->code);
            [$this->namespace, $this->function, $parameters, $returnedType, $this->bodyStartsAt] = ElementUtils::getFunctionDefinition($this->code);
        }

        return $this->code !== null;
    }

    public abstract function makeComponent(string $filename, string &$html): void;

    public static function createByHtml(string $html): static
    {
        $new = new static;
        $new->code = $html;

        return $new;
    }

    /**
     * @throws Exception
     */
    public function getEntity(): ?ComponentEntity
    {
        if ($this->entity === null) {
            $this->setEntity();
        }

        return $this->entity;
    }

    /**
     * @throws Exception
     */
    protected function setEntity(): void
    {
        $decl = $this->getDeclaration();
        $this->entity = $decl->getComposition();
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @throws Exception
     */
    public function getDeclaration(): ?ComponentDeclaration
    {
        if ($this->declaration === null) {
            $this->setDeclaration();
        }

        return $this->declaration;
    }

    /**
     * @throws Exception
     */
    protected function setDeclaration(): void
    {
        $fqName = ComponentRegistry::read($this->uid);

        if ($fqName === null) {
            $fqName = $this->getFullyQualifiedFunction();
            if ($fqName === null) {
                throw new Exception('Please the component is defined in the registry before asking for its entity');
            }
        }
        CodeRegistry::setCacheDirectory(CACHE_DIR . $this->getMotherUID());

        $list = CodeRegistry::read($fqName);
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        $this->declaration = $decl;
    }

    public function resetDeclaration(): void
    {
        $this->declaration = null;
    }

    public function composedOfUnique(): ?array
    {
        $result = $this->composedOf();

        if ($result === null) return null;

        return array_unique($result);
    }

    public function composedOf(): ?array
    {
        $names = [];

        $this->forEach(function (ComponentEntityInterface $item, $key) use (&$names) {
            $names[] = $item;
        }, $this);

        $names = array_filter($names, function ($item) {
            return $item !== null;
        });

        if (count($names) === 0) {
            $names = null;
        }

        return $names;
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
    public function render(array|object|null $functionArgs = null, ?Request $request = null): void
    {
        if ($this->motherUID == $this->uid && $this->id !== 'App') {
            StateRegistry::loadByMotherUid($this->motherUID, true);
//            StateRegistry::load(true);
            $stateIgniter = new ApplicationIgniter;
            $stateIgniter->ignite();
        }

//        $trueStaticFile = STATIC_DIR . pathinfo($this->filename, PATHINFO_FILENAME) . PREHTML_EXTENSION;
//        if ($html = File::safeRead($trueStaticFile) != null && $this->motherUID == $this->uid && $this->id !== 'App')  {
//            echo $html;
//            return;
//        }

        [$fqFunctionName, $cacheFilename] = $this->renderComponent($this->motherUID, $this->function, $functionArgs);
        $html = ComponentRenderer::renderHTML($cacheFilename, $fqFunctionName, $functionArgs, $request);
        if ($this->motherUID == $this->uid && $this->id !== 'App') {
            File::safeWrite(STATIC_DIR . $this->filename, $html);
        }
        echo $html;

    }

    public function renderComponent(string $motherUID, string $functionName, array|object|null $functionArgs = null): array
    {
        [$fqFunctionName, $cacheFilename, $isCached] = ComponentFinder::find($functionName, $motherUID);
        if (!$isCached) {
            ComponentRegistry::load();

            $fqName = ComponentRegistry::read($functionName);
            $component = ComponentFactory::create($fqName, $motherUID);
            $component->parse();

            $motherUID = $component->getMotherUID();

            $cacheFilename = $motherUID . DIRECTORY_SEPARATOR . $component->getSourceFilename();
        }

        return [$fqFunctionName, $cacheFilename];
    }

    public function parse(): void
    {
        ApplicationRecursiveParser::parse($this);
    }

    public function getSourceFilename(): string
    {
        return $this->filename;
    }

    public function getFlattenSourceFilename(): string
    {
        return static::getFlatFilename($this->filename);
    }

    public static function getFlatFilename(string $basename): string
    {
        return str_replace('/', '_', $basename);
    }

    public function getFlattenFilename(): string
    {
        return static::getFlatFilename($this->filename);
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

        return ComponentsCopier::copy($list, $motherUID, $component);
    }

    protected function cacheHtml(): ?string
    {
        return $this->cacheFile(CACHE_DIR);
    }

    private function cacheFile($cacheDir): ?string
    {
        $cache_file = $this->getSourceFilename();
        $result = File::safeWrite($cacheDir . $this->motherUID . DIRECTORY_SEPARATOR . $cache_file, $this->code);

        $cache = (($cache = CacheRegistry::read($this->motherUID)) === null) ? [] : $cache;

        $cache[$this->getFullyQualifiedFunction()] = $this->getSourceFilename();
        CacheRegistry::write($this->motherUID, $cache);
        CacheRegistry::save();

        return $result === null ? $result : $cache_file;
    }

    public function getFullyQualifiedFunction(): ?string
    {
        return $this->namespace . '\\' . $this->function;
    }

    protected function cacheJavascript(): ?string
    {
        return $this->cacheFile(RUNTIME_JS_DIR);
    }
}