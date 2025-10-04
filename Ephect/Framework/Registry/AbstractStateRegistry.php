<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

abstract class AbstractStateRegistry extends AbstractRegistry implements RegistryInterface
{
    protected function __saveByMotherUid(string $motherUid, bool $asArray = false): void
    {
        $entries = $this->__items();

        $result = json_encode($entries, JSON_PRETTY_PRINT);

        if ($asArray) {
            $result = Text::jsonToPhpReturnedArray($result);
        }

        $this->__setCacheDirectory(\Constants::CACHE_DIR . $motherUid);
        $registryFilename = $this->__getCacheFileName($asArray);
        $len = File::safeWrite($registryFilename, $result);
    }

    protected function __loadByMotherUid(string $motherUid, bool $asArray = false): void
    {
        $this->__setCacheDirectory(\Constants::CACHE_DIR . $motherUid);
        $this->__load($asArray);
    }

    protected function __writeItem(string|int $item, ...$params): void
    {
        $concat = function (string|int $key, mixed $value) use ($item): mixed {
            $result = $value;
            if (isset($this->entries[$item][$key]) && is_array($this->entries[$item][$key]) && is_array($value)) {
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

    protected function __unshift(string|int $item, string|int $key, mixed $value): void
    {
        if (!isset($this->entries[$item])) {
            $this->push($item, $key, $value);
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

    protected function __push(string|int $item, string|int $key, mixed $value): void
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

        $this->entries[$item][$key][] = $value;
    }

    protected function __keys(string|null $item = null): array
    {
        if ($item === null) {
            return array_keys($this->entries);
        } elseif (is_array($this->entries)) {
            return array_keys($this->entries[$item]);
        } else {
            return [];
        }
    }

    protected function __item(string|int $item, string|null $value = null): ?array
    {
        if ($item === '' || $item === null) {
            return null;
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

        return null;
    }

    protected function __ini(string $section, string|null $key = null): string|null
    {
        $section = $this->__readItem('ini', $section);
        $value = null;

        if ($key === null) {
            return $section;
        }

        if (is_array($section)) {
            $value = $section[$key] ?? $value;
        }

        return $value;
    }

    protected function __readItem(string|int $item, string|int $key, mixed $defaultValue = null): mixed
    {
        $result = null;

        if ($this->entries[$item] !== null) {
            $result = $this->entries[$item][$key] ?? (($defaultValue !== null) ? $defaultValue : null);
        }

        return $result;
    }
}
