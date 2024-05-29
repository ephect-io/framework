<?php

namespace Ephect\Framework\Components;

use BadFunctionCallException;
use Ephect\Framework\ElementTrait;
use Ephect\Plugins\Router\RouterService;
use Ephect\Framework\Registry\CacheRegistry;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Tree\Tree;
use Ephect\Framework\Web\Request;
use Exception;
use ReflectionException;
use ReflectionFunction;
use stdClass;
//use tidy;

abstract class AbstractComponent extends Tree implements ComponentInterface
{
    use ElementTrait;

    protected ?string $code;
    protected ?stdClass $children = null;
    protected ?ComponentDeclaration $declaration = null;
    protected ?ComponentEntity $entity = null;
    protected int $bodyStartsAt = 0;

    public function getBodyStart(): int
    {
        return $this->bodyStartsAt;
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
        return $this->namespace . '\\' . $this->function;
    }

    public function getFunction(): ?string
    {
        return $this->function;
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

    public function findComponent(string $componentName, string $motherUID): array
    {
        ComponentRegistry::uncache();
        $uses = ComponentRegistry::items();
        $fqFuncName = $uses[$componentName] ?? null;

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

    /**
     * @throws ReflectionException
     */
    public function renderHTML(string $cacheFilename, string $fqFunctionName, ?array $functionArgs = null, ?Request $request = null): string
    {
        include_once CACHE_DIR . $cacheFilename;

        $funcReflection = new ReflectionFunction($fqFunctionName);
        $funcParams = $funcReflection->getParameters();

        $bodyProps = null;
        if($request !== null && $request->headers->contains('application/json', 'content-type')) {
            $bodyProps = json_decode($request->body);
        }

        $html = '';

        if ($funcParams === [] && $bodyProps === null) {
            ob_start();
            $fn = call_user_func($fqFunctionName);
            $fn();
            $html = ob_get_clean();
        } else {
            $props = null;
            if (count($functionArgs) > 0) {
                $props = $functionArgs;
            } else {
                $routeProps = RouterService::findRouteArguments($fqFunctionName);
                if ($routeProps !== null) {
                    $props = new stdClass;
                    foreach ($routeProps as $field => $value) {
                        $props->{$field} = null;
                    }
                }
            }

            if($bodyProps !== null) {
                if($props === null) {
                    $props = new stdClass;
                }
                foreach ($bodyProps as $field => $value) {
                    $props->{$field} = $value;
                }
            }
            ob_start();
            $fn = call_user_func($fqFunctionName, $props);
            $fn();
            $html = ob_get_clean();

        }

        return $html;
    }


}
