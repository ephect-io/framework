<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\StaticElement;

/**
 * Description of registry
 *
 * @author david
 */
class StateRegistry extends StaticElement
{
    private static $_items = [];

    /**
     * @param mixed $item Name of the key
     * @param array $params May one key/value pair or an array of pairs
     * @return void
     */
    public static function write($item, ...$params): void
    {
        if (!isset(self::$_items[$item])) {
            self::$_items[$item] = [];
        }

        if (is_array($params) && count($params) === 1) {
            $param0 = $params[0];
            if (is_object($param0)) {
                $param0 = json_encode($param0);
                $param0 = json_decode($param0, JSON_OBJECT_AS_ARRAY);
            }
            if (is_array($param0) && count($param0) > 0) {
                foreach ($param0 as $key => $value) {
                    self::$_items[$item][$key] = $value;
                }
            }
        }

        if (count($params) === 2) {
            $key = $params[0];
            $value = $params[1];
            self::$_items[$item][$key] = $value;
        }
    }

    public static function unshift($item, $key, $value): void
    {
        if (!isset(self::$_items[$item])) {
            self::push($item, $key, $value);
        }

        if (!isset(self::$_items[$item][$key])) {
            self::$_items[$item][$key] = $value;
        }

        if (isset(self::$_items[$item][$key]) && !is_array(self::$_items[$item][$key])) {
            $tmp = self::$_items[$item][$key];
            self::$_items[$item][$key] = [];
            self::$_items[$item][$key][] = $tmp;
        }

        array_unshift(self::$_items[$item][$key], $value);
    }

    public static function push($item, $key, $value): void
    {
        if (!isset(self::$_items[$item])) {
            self::$_items[$item] = [];
        }

        if (!isset(self::$_items[$item][$key])) {
            self::$_items[$item][$key] = $value;
        }

        if (isset(self::$_items[$item][$key]) && !is_array(self::$_items[$item][$key])) {
            $tmp = self::$_items[$item][$key];
            self::$_items[$item][$key] = [];
            self::$_items[$item][$key][] = $tmp;
        }

        array_push(self::$_items[$item][$key], $value);
    }

    public static function read($item, $key, $defaultValue = null)
    {
        $result = null;

        if (self::$_items[$item] !== null) {
            $result = self::$_items[$item][$key] ?? (($defaultValue !== null) ? $defaultValue : null);
        }

        return $result;
    }

    public static function remove($item): void
    {
        if (array_key_exists($item, self::$_items)) {
            unset(self::$_items[$item]);
        }
    }

    public static function keys($item = null): array
    {
        if ($item === null) {
            return array_keys(self::$_items);
        } elseif (is_array(self::$_items)) {
            return array_keys(self::$_items[$item]);
        } else {
            return [];
        }
    }

    public static function exists($item, $key = null): bool
    {
        return isset(self::$_items[$item][$key]);
    }

    public static function clear(): void
    {
        StateRegistry::$_items = [];
    }

    public static function dump(string $key): void
    {
        self::getLogger()->dump('Registry key ' . $key, StateRegistry::item($key));
    }

    public static function item($item, $value = null): ?array
    {
        if ($item === '' || $item === null) {
            return $item;
        }

        if (isset(self::$_items[$item])) {
            if ($value != null) {
                self::$_items[$item] = $value;
            } else {
                return self::$_items[$item];
            }
        }
        if (!isset(self::$_items[$item])) {
            self::$_items[$item] = [];
            return self::$_items[$item];
        }
    }
}
