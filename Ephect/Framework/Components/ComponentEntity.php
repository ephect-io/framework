<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Tree\Tree;

/**
 * Description of match
 *
 * @author david
 */
class ComponentEntity extends Tree implements ComponentEntityInterface
{
    use ElementTrait;

    protected int $parentId = 0;
    protected string $name = '';
    protected string $text = '';
    protected int $start = 0;
    protected int $end = 0;
    protected int $depth = 0;
    protected bool $isSibling = false;
    protected ?array $closer = null;
    protected mixed $contents = null;
    protected bool $hasCloser = false;
    protected bool $hasProperties = false;
    protected array $properties = [];
    protected string $method = '';
    protected string $compName = '';
    protected ?string $className = '';
    protected bool $isSingle = false;

    public function __construct(protected ?ComponentStructure $attributes)
    {
        parent::__construct([]);

        if ($attributes === null) {
            return null;
        }

        $this->uid = $attributes->uid;
        $this->motherUID = $attributes->motherUID;
        $this->id = $attributes->id;
        $this->className = $attributes->class;
        $this->compName = $attributes->component;
        $this->parentId = $attributes->parentId;
        $this->text = $attributes->text;
        $this->name = $attributes->name;
        $this->method = $attributes->method;
        $this->start = $attributes->startsAt;
        $this->end = $attributes->endsAt;
        $this->depth = $attributes->depth;
        $this->hasProperties = count($attributes->props) !== 0;
        $this->properties = $this->hasProperties ? $attributes->props : [];
        $this->hasCloser = is_array($attributes->closer);
        $this->closer = $this->hasCloser ? $attributes->closer : null;
        $this->isSingle = $attributes->isSingle;

        $this->elementList = (false === $attributes->node) ? [] : $attributes->node;

        $this->bindNode();
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

    public static function buildFromArray(?array $list): ?ComponentEntity
    {
        $result = null;

        if ($list === null) {
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
            $list[$pId]['node'][] = $list[$i];
            unset($list[$i]);
        }

        if (count($list) === 1) {
            $result = new ComponentEntity(new ComponentStructure($list[0]));
        } elseif (count($list) > 1) {
            $result = self::_makeFragment();
            foreach ($list as $item) {
                $entity = new ComponentEntity(new ComponentStructure($item));
                $result->add($entity);
            }
        }

        return $result;
    }

    private static function listIdsByDepth(?array $list): ?array
    {
        if ($list === null) {
            return null;
        }

        $result = [];

        $depths = [];

        foreach ($list as $match) {

            $struct = new ComponentStructure($match);
            $depths[$struct->depth] = 1;
        }

        $maxDepth = count($depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    $result[] = $match['id'];
                }
            }
        }

        return $result;
    }

    private static function _makeFragment(): ComponentEntityInterface
    {
        $json = <<<JSON
            {
                "closer": {
                    "id": 1,
                    "parentId": 0,
                    "text": "<\/>",
                    "startsAt": 0,
                    "endsAt": 0,
                    "contents": {
                        "startsAt": 0,
                        "endsAt": 0
                    }
                },
                "uid": "00000000-0000-0000-0000-000000000000",
                "id": 0,
                "name": "FakeFragment",
                "class": null,
                "component": "Ephect",
                "text": "<>",
                "method": "echo",
                "startsAt": 0,
                "endsAt": 0,
                "props": [],
                "node": false,
                "hasCloser": true,
                "isSibling": false,
                "parentId": -1,
                "depth": 0
            }
        JSON;

        $fragment = json_decode($json, JSON_OBJECT_AS_ARRAY);

        return new ComponentEntity(new ComponentStructure($fragment));

    }

    public function getParentId(): int
    {
        return $this->parentId;
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

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function composedOf(): array
    {
        $names = [];
        $names[] = $this->name;

        $this->forEach(function (ComponentEntityInterface $item, $key) use (&$names) {
            $names[] = $item->getName();
        }, $this);

        return $names;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return $this->attributes->toArray();
    }

    public function props(?string $key = null): string|array|null
    {
        if ($key === null) {
            if (count($this->properties) === 0) {
                return null;
            }
            return $this->properties;
        }
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
        return null;
    }

    public function getInnerHTML(): string
    {
        $result = '';

        if (!isset($this->closer['contents']['text'])) {
            return $result;
        }

        $result = $this->closer['contents']['text'];
        $result = substr($result, 9);
        $result = base64_decode($result);

        return $result;
    }
}
