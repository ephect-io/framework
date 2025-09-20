<?php
namespace Ephect\Modules\DataAccess\LINQ\Factory;


use Ephect\Modules\DataAccess\LINQ\Helper\IJoinHelper;
use Ephect\Modules\DataAccess\LINQ\Helper\JoinHelper;
use Ephect\Modules\DataAccess\LINQ\Helper\LeftJoinHelper;

/**
 * Class JoinFactory
 *
 * @package Linq\Factory
 */
class JoinFactory
{
    /** ENUM */
    const string INNER = "inner";
    const string LEFT = "left";

    /** @var array<IJoinHelper>  */
    protected array $joinObjects = [];

    /**
     * JoinFactory constructor.
     */
    public function __construct() {
        $this->joinObjects = array(
            "inner" => new JoinHelper(),
            "left" =>  new LeftJoinHelper()
        );
    }

    /**
     * @param mixed $type
     * @return IJoinHelper
     * @throws \Exception
     */
    public function getJoinObject(mixed $type): array
    {
        if(!in_array($type, array_keys($this->joinObjects))){
            throw new \Exception("Invalid Join Type Parametr");
        }
        return $this->joinObjects[$type];
    }
}