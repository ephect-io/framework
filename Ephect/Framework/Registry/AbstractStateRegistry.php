<?php

namespace Ephect\Framework\Registry;


use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

abstract class AbstractStateRegistry extends AbstractRegistry implements RegistryInterface
{

    public function _saveByMotherUid(string $motherUid, bool $asArray = false): void
    {
        $entries = $this->_items();

        $result = json_encode($entries, JSON_PRETTY_PRINT);

        if ($asArray) {
            $result = Text::jsonToPhpReturnedArray($result);
        }

        $this->_setCacheDirectory(CACHE_DIR . DIRECTORY_SEPARATOR . $motherUid);
        $registryFilename =  $this->_getCacheFileName($asArray);
        $len = File::safeWrite($registryFilename, $result);

    }

    public function _loadByMotherUid(string $motherUid, bool $asArray = false): void
    {
        $this->_setCacheDirectory(CACHE_DIR . DIRECTORY_SEPARATOR . $motherUid);
        $this->_load($asArray);
    }

    public function _readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed
    {
        $result = null;

        if ($this->entries[$item] !== null) {
            $result = $this->entries[$item][$key] ?? (($defaultValue !== null) ? $defaultValue : null);
        }

        return $result;
    }

    public function _writeItem(string|int $item, ...$params): void
    {
        $concat = function (string|int $key, mixed $value) use ($item): mixed {
            $result = $value;
            if(isset($this->entries[$item][$key]) && is_array($this->entries[$item][$key]) && is_array($value)) {
                $result = array_merge_recursive($this->entries[$item][$key], $value);
            }

            return $result;
        };

        if (!isset($this->entries[$item])) {
            $this->entries[$item] = [];
        }

        if (count($params) === 1) {
            $param0 = $params[0];
            if (is_object($param0)) {
                $param0 = json_encode($param0);
                $param0 = json_decode($param0, JSON_OBJECT_AS_ARRAY);
            }
            if (is_array($param0) && count($param0) > 0) {
                foreach ($param0 as $key => $value) {
                    $this->entries[$item][$key] = $concat($key, $value);
                }
            }
        }

        if (count($params) === 2) {
            $key = $params[0];
            $value = $params[1];
            $this->entries[$item][$key] = $concat($key, $value);
        }
    }

    public function _unshift(string|int $item, string|int $key, mixed $value): void
    {
        if (!isset($this->entries[$item])) {
            self::push($item, $key, $value);
        }

        if (!isset($this->entries[$item][$key])) {
            $this->entries[$item][$key] = $value;
        }

        if (isset($this->entries[$item][$key]) && !is_array($this->entries[$item][$key])) {
            $tmp = $this->entries[$item][$key];
            $this->entries[$item][$key] = [];
            $this->entries[$item][$key][] = $tmp;
        }

        array_unshift($this->entries[$item][$key], $value);
    }

    public function _push(string|int $item, string|int $key, mixed $value): void
    {
        if (!isset($this->entries[$item])) {
            $this->entries[$item] = [];
        }

        if (!isset($this->entries[$item][$key])) {
            $this->entries[$item][$key] = $value;
        }

        if (isset($this->entries[$item][$key]) && !is_array($this->entries[$item][$key])) {
            $tmp = $this->entries[$item][$key];
            $this->entries[$item][$key] = [];
            $this->entries[$item][$key][] = $tmp;
        }

        array_push($this->entries[$item][$key], $value);
    }

    public function _keys(string|null $item = null): array
    {
        if ($item === null) {
            return array_keys($this->entries);
        } elseif (is_array($this->entries)) {
            return array_keys($this->entries[$item]);
        } else {
            return [];
        }
    }

    public function _item(string|int $item, string|null $value = null): ?array
    {
        if ($item === '' || $item === null) {
            return $item;
        }

        if (isset($this->entries[$item])) {
            if ($value != null) {
                $this->entries[$item] = $value;
            } else {
                return $this->entries[$item];
            }
        }
        if (!isset($this->entries[$item])) {
            $this->entries[$item] = [];
            return $this->entries[$item];
        }
    }

    public function _ini(string $section, string|null $key = null): string|null
    {
        $section = $this->_readItem('ini', $section);
        $value = null;

        if ($key === null) {
            return $section;
        }

        if (is_array($section)) {
            $value = $section[$key] ?? $value;
        }

        return $value;
    }
}
