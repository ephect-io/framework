<?php

namespace FunCom\Registry;

use FunCom\StaticElement;

/**
 * Description of registry
 *
 * @author david
 */

class Registry extends StaticElement
{
    private static $_items = [];
    private static $_isInit = false;

    public static function init(): bool
    {
        if (self::$_isInit) {
            return true;
        }

        self::$_isInit = true;

        return self::$_isInit;
    }

    public static function classInfo(string &$className = '')
    {
        $result = null;

        return $result;
    }

    public static function widgetPath($className): string
    {
        $result = '';

        return $result;
    }

    public static function classPath($className = ''): string
    {
        $classInfo = self::classInfo($className);
        return ($classInfo) ? $classInfo->path : '';
    }

    public static function classNamespace($className = ''): bool
    {
        $classInfo = self::classInfo($className);
        return ($classInfo) ? $classInfo->namespace : '';
    }

    public static function classHasTemplate($className = ''): bool
    {
        $classInfo = self::classInfo($className);
        return ($classInfo) ? $classInfo->hasTemplate : false;
    }

    public static function classCanRender($className = ''): bool
    {
        $classInfo = self::classInfo($className);
        return ($classInfo) ? $classInfo->canRender : '';
    }

    public static function getCode($id): string
    {
        return self::$_items['code'][$id];
    }

    public static function setCode($id, $value): void
    {
        self::write('code', $id, $value);
    }

    public static function getHtml($id): string
    {
        return self::$_items['html'][$id];
    }

    public static function setHtml($id, $value): void
    {

        self::write('html', $id, $value);
    }

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
        if (count($params) === 2) {
            $key = $params[0];
            $value = $params[1];
            self::$_items[$item][$key] = $value;
        }
        if (count($params) === 1 && is_array($params)) {
            if (count($params[0]) > 0 && is_array($params[0])) {
                foreach ($params[0] as $key => $value) {
                    self::$_items[$item][$key] = $value;
                }
            }
        }
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

    public static function read($item, $key, $defaultValue = null)
    {
        $result = null;

        if (self::$_items[$item] !== null) {
            $result = isset(self::$_items[$item][$key]) ? self::$_items[$item][$key] : (($defaultValue !== null) ? $defaultValue : null);
        }

        return $result;
    }

    public static function ini($section, $key = null)
    {
        $section = self::read('ini', $section);
        $value = null;

        if ($key === null) {
            return $section;
        }

        if (is_array($section)) {
            $value = isset($section[$key]) ? $section[$key] : $value;
        }

        return $value;
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

    public static function exists($item, $key = null): bool
    {
        return isset(self::$_items[$item][$key]);
    }

    public static function clear(): void
    {
        Registry::$_items = [];
    }

    public static function dump(string $key): void
    {
        self::getLogger()->dump('Registry key ' . $key, Registry::item($key));
    }
}
