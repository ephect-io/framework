<?php
namespace FunCom;

use \ReflectionClass;

class Element extends StaticElement implements ElementInterface
{
    private $_reflection = null;
    protected $parent = null;
    protected $uid = '';
    protected $id = 'noname';
    protected $serialFilename = '';
    protected $isSerialized = '';
    protected $children = [];
    protected $fqClassName = '';
    protected $type = '';

    public function __construct(ElementInterface $parent = null)
    {
        $this->parent = $parent;
        $this->uid = uniqid(rand(), true);
    }

    public function getUID(): string
    {
        return $this->uid;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($value): void
    {
        $this->id = $value;
    }

    public function isAwake(): bool
    {
        return $this->isSerialized;
    }

    public function getReflection(): ?ReflectionClass
    {
        if ($this->_reflection == NULL) {
            $this->_reflection = new ReflectionClass(get_class($this));
        }
        return $this->_reflection;
    }

    public function getMethodParameters($method): ?array
    {
        $ref = $this->getReflection();
        $met = $ref->getMethod($method);

        $params = [];
        foreach ($met->getParameters() as $currentParam) {
            array_push($params, $currentParam->name);
        }

        return $params;
    }

    public function getParent(): ?ElementInterface
    {
        return $this->parent;
    }

    public function addChild(ElementInterface $child)
    {
        $this->children[$child->getId()] = $child;
    }

    public function removeChild(ElementInterface $child): void
    {
        unset($this->children[$child->getId()]);
    }

    public function getChildById($id): ?object
    {
        $result = null;

        if (array_key_exists($id, $this->children)) {
            $result = $this->children[$id];
        }

        return $result;
    }

    public function getChildrenIds(): ?array
    {
        return array_keys($this->children);
    }

    public function getFullType(): string
    {
        return get_class($this);
    }

    public function getNamespace(): string
    {
        $typeParts = explode('\\', $this->getFQClassName());
        array_pop($typeParts);

        $result = (count($typeParts) > 0) ? implode('\\', $typeParts) : '';

        return $result;
    }

    public function getFQClassName(): string
    {
        if ($this->fqClassName == '') {
            $this->fqClassName = get_class($this);
        }

        return $this->fqClassName;
    }

    public function getType(): string
    {
        if($this->type === '') {
            $typeParts = explode('\\', $this->getFQClassName());
            $this->type = array_pop($typeParts);
        }

        return $this->type;
    }

    public function getBaseType(): string
    {
        return get_parent_class($this);
    }

    public function getFileName(): string
    {
        $reflection = $this->getReflection();
        return $reflection->getFileName();
    }

    // public function validate($method)
    // {
    //     if ($method == '') return false;

    //     $result = [];

    //     if (!method_exists($this, $method)) {
    //         throw new \BadMethodCallException($this->getFQClassName() . "::$method is undefined");
    //     } else {

    //         $params = $this->getMethodParameters($method);

    //         $args = $_REQUEST;
    //         if (isset($args['PHPSESSID'])) unset($args['PHPSESSID']);
    //         if (isset($args['action'])) unset($args['action']);
    //         if (isset($args['token'])) unset($args['token']);
    //         if (isset($args['q'])) unset($args['q']);
    //         if (isset($args['_'])) unset($args['_']);
    //         $args = array_keys($args);

    //         $validArgs = [];
    //         foreach ($args as $arg) {
    //             if (!in_array($arg, $params)) {
    //                 throw new \BadMethodCallException($this->getFQClassName() . "::$method::$arg is undefined");
    //             } else {
    //                 array_push($validArgs, $arg);
    //             }
    //         }
    //         foreach ($params as $param) {
    //             if (!in_array($param, $validArgs)) {
    //                 throw new \BadMethodCallException($this->getFQClassName() . "::$method::$param is missing");
    //             } else {
    //                 $result[$param] = \Phink\Web\TRequest::getQueryStrinng($param);
    //             }
    //         }
    //     }

    //     return $result;
    // }

    // public function invoke($method, $params = array())
    // {
    //     $result = null;
    //     $values = array_values($params);

    //     if (count($values) > 0) {
    //         $args = '"' . implode('", "', $values) . '"';
    //         $ref = new \ReflectionMethod($this->getFQClassName(), $method);
    //         $result = $ref->invokeArgs($this, $values);
    //     } else {
    //         $result = $this->$method();
    //     }

    //     return $result;
    // }

}
