<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Entity\Entity;
use Ephect\Framework\Tree\TreeTrait;

class Children extends Entity implements ChildrenInterface
{
    use ElementTrait;
    use TreeTrait;

    protected ?object $props = null;
    protected array|object|null $parentProps = null;
    protected array|object|null $allProps = null;
    protected $buffer = null;

    protected ?string $name = null;

    public function __construct(ChildrenStructure $struct)
    {
        parent::__construct($struct);

        $this->uid = $struct->uid;
        $this->motherUID = $struct->motherUID;
        $this->props = $struct->props;
        $this->parentProps = $struct->parentProps;
        $this->name = $struct->name;
        $this->class = $struct->class;
        $this->buffer = $struct->buffer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function props(): array|object
    {
        return $this->props;
    }

    public function buffer(): callable
    {
        return $this->buffer;
    }

    public function getBuffer(): string
    {
        ob_start();
        $fn = $this->buffer;
        call_user_func($fn);
        return ob_get_clean();
    }

    public function render(): void
    {
        $fn = $this->buffer;
        call_user_func($fn);
    }

    public function getAttributes(): string
    {
        $props = $this->props->props ?? $this->props;

        $args = [];
        foreach ($props as $key => $value) {
            $args[] = $key . '="' . $value . '"';
        }

        return implode(" ", $args);

    }

    public function getAttribute(string $attribute): string|bool|null
    {
        $props = $this->getAllProps();
        $value = $props->$attribute ?? null;

        if ($value === 'false') {
            $value = false;
        }
        if ($value === 'true') {
            $value = true;
        }
        return $value;
    }

    public function getAllProps(): array|object
    {
        $parentProps = $this->parentProps();
        $props = $this->props;

        $parentProps = json_encode($parentProps);
        $props = json_encode($props);

        $parentProps = json_decode($parentProps, JSON_OBJECT_AS_ARRAY);
        $props = json_decode($props, JSON_OBJECT_AS_ARRAY);

        $allProps = array_merge(
            $parentProps,
            $props
        );

        return (object)$allProps;
    }

    public function parentProps(): array|object
    {
        if ($this->parentProps === null && isset($this->props->props)) {
            $this->parentProps = $this->props->props;
        }

        return $this->parentProps;
    }

}
