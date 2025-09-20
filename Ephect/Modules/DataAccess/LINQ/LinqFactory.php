<?php
    /**
     * User: Milan Gallas
     * Date: 6.4.2016
     * Time: 8:49
     */

    namespace Ephect\Modules\DataAccess\LINQ;

    use Ephect\Modules\DataAccess\LINQ\Factory\JoinFactory;

    class LinqFactory
    {
        /** @var JoinFactory|array */
        protected static JoinFactory|array $linqs = array();

        /**
         * return basic Linq object
         * @return Linq
         */
        public static function createLinq(): Linq
        {
            if(!isset(self::$linqs["linq"])){
                self::$linqs["linq"] = new Linq( new JoinFactory() );
            }
            return self::$linqs["linq"];
        }

        /**
         * return JsonLinq object
         * @return JsonLinq
         */
        public static function createJsonLinq(): JsonLinq
        {
            if(!isset(self::$linqs["jsonLinq"])){
                self::$linqs["jsonLinq"] = new JsonLinq( new JoinFactory() );
            }
            return self::$linqs["jsonLinq"];
        }

        /**
         * return XmlLinq object
         * @return XmlLinq
         */
        public static function createXmlLinq(): XmlLinq
        {
            if(!isset(self::$linqs["xmlLinq"])){
                self::$linqs["xmlLinq"] = new XmlLinq( new JoinFactory() );
            }
            return self::$linqs["xmlLinq"];
        }
    }