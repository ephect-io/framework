<?php

namespace Ephect\Components;

use Ephect\Components\ComponentStructure;
use Ephect\ElementTrait;
use Ephect\IO\Utils;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\Tree;

/**
 * Description of match
 *
 * @author david
 */
class ComponentEntity extends Tree implements ComponentEntityInterface
{
    use ElementTrait;

    protected $parentId = 0;
    protected $name = '';
    protected $text = '';
    protected $start = 0;
    protected $end = 0;
    protected $depth = 0;
    protected $isSibling = false;
    protected $closer = '';
    protected $contents = null;
    protected $hasCloser = '';
    protected $hasProperties = false;
    protected $properties = [];
    protected $method = '';
    protected $doc = null;
    protected $compName = '';
    protected $className = '';
    protected $attributes = null;
    protected $composedOf = null;

    public function __construct(?ComponentStructure $attributes)
    {
        if ($attributes === null) {
            return null;
        }

        $this->id = $attributes->id;
        $this->className = $attributes->class;
        $this->componentName = $attributes->component;
        $this->parentId = $attributes->parentId;
        $this->text = $attributes->text;
        $this->name = $attributes->name;
        $this->method = $attributes->method;
        $this->start = $attributes->startsAt;
        $this->end = $attributes->endsAt;
        $this->depth = $attributes->depth;
        $this->properties = $attributes->props;
        $this->hasProperties = count($this->properties) !== 0;
        $this->closer = $attributes->closer;
        $this->hasCloser = is_array($this->closer);
        $this->contents = $this->hasCloser ? $this->closer['contents'] : null;
        $this->attributes = $attributes;

        $this->elementList = (false === $attributes->node) ? [] : $attributes->node;

        $this->bindNode();
    }

    private static function listIdsByDepth(?array $list): ?array
    {
        if ($list === null) {
            return null;
        }

        $result = [];

        $structs = [];
        $depths = [];

        foreach ($list as $match) {

            $struct = new ComponentStructure($match);
            array_push($structs, $struct);
            $depths[$struct->depth] = 1;
        }

        $maxDepth = count($depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    array_push($result, $match['id']);
                }
            }
        }

        return $result;
    }

    public static function buildFromArray(?array $list): ?ComponentEntity
    {
        $result = null;

        if($list === null) {
            return null;
        }

        $depthIds = static::listIdsByDepth($list);

        $c = count($list);

        for ($j = 0; $j < $c; $j++) {
            $i = $depthIds[$j];
            if ($list[$i]['parentId'] === -1) {
                continue;
            }
            $pId = $list[$i]['parentId'];

            if (!is_array($list[$pId]['node'])) {
                $list[$pId]['node'] = [];
            }
            array_push($list[$pId]['node'], $list[$i]);
            unset($list[$i]);
        }

        if (count($list) > 0) {
            $result = new ComponentEntity(new ComponentStructure($list[0]));
        }

        return $result;
    }

    public function composedOf(): array
    {
        $names = [];
        array_push($names, $this->name);

        $this->forEach(function (ComponentEntityInterface $item, $key) use (&$names) {
            array_push($names, $item->getName());
        }, $this);

        return $names;
    }

    public function composedOfUnique(): array
    {
        $result = $this->composedOf();


        $result = array_unique($result);

        return $result;
    }

    public function bindNode(): void
    {
        if ($this->elementList === false || $this->elementList === null) {
            return;
        }

        $this->elementList = array_map(function ($child) {
            return new ComponentEntity(new ComponentStructure($child));
        }, $this->elementList);
    }

    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function hasProps(): bool
    {
        return count($this->properties) > 0;
    }
    
    public function props(?string $key = null): string|array|null
    {
        if($key === null) {
            if(count($this->properties) === 0) {
                return null;
            }
            return $this->properties;
        }
        if (isset($this->properties[$key])) {
            $result = $this->properties[$key];
        }
        return $result;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getContents(?string $html = null): ?string
    {
        if ($this->contents === null) {
            return null;
        }

        $s = $this->contents['startsAt'];
        $e = $this->contents['endsAt'];

        if ($e - $s < 1) {
            return '';
        }

        ComponentRegistry::uncache();
        $compFile = ComponentRegistry::read($this->componentName);
        if ($compFile === null) {
            return null;
        }
        $t = $html ?: Utils::safeRead(COPY_DIR . $compFile);

        if(($p = strpos($t, $this->name)) > $s) {
            $o =  $p - $s - 1;

            $s += $o;
            $e += $o;
        }

        $contents = substr($t, $s, $e - $s + 1);

        return $contents;
    }

    public function getChildName(): string
    {
        return $this->childName;
    }

    public function hasCloser(): bool
    {
        return $this->hasCloser;
    }

    public function isSibling(): bool
    {
        return $this->isSibling;
    }

    public function getCloser(): array
    {
        return $this->closer;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
