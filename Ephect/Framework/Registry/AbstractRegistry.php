<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Utils\TextUtils;

abstract class AbstractRegistry implements AbstractRegistryInterface
{
    private $entries = [];
    private $isLoaded = false;
    private $baseDirectory = CACHE_DIR;
    private $cacheFilename = '';
    private $flatFilename = '';

    use ElementTrait;

    public function _items(): array
    {
        return $this->entries;
    }

    public function _write(string $key, $value): void
    {
        if (!isset($this->entries[$key])) {
            $this->entries[$key] = null;
        }
        $this->entries[$key] = $value;
    }

    public function _read($key, $value = null)
    {
        if (!isset($this->entries[$key])) {
            return null;
        }

        if ($value === null) {
            return $this->entries[$key];
        }

        if (!isset($this->entries[$key][$value])) {
            return null;
        }

        return $this->entries[$key][$value];
    }

    public function _delete(string $key): void
    {
        unset($this->entries[$key]);
    }

    public function _exists(string $key): bool
    {
        $result =  isset($this->entries[$key]);

        return $result;
    }

    public function _cache(bool $asArray = false): bool
    {
        $result = '';

        $entries = $this->_items();

        $result = json_encode($entries, JSON_PRETTY_PRINT);

        if ($asArray) {
            $result = TextUtils::jsonToPhpArray($result);
            $result = str_replace('"' . EPHECT_ROOT, 'EPHECT_ROOT . "', $result);
            $result = str_replace('"' . SRC_ROOT, 'SRC_ROOT . "', $result);
        }

        $registryFilename = $this->_getCacheFileName($asArray);
        $len = Utils::safeWrite($registryFilename, $result);

        return $len !== null;
    }

    public function _uncache(bool $asArray = false): bool
    {
        $this->isLoaded = false;

        $registryFilename = $this->_getCacheFileName($asArray);
        $text = Utils::safeRead($registryFilename);
        $this->isLoaded = $text !== null;

        if ($this->isLoaded && !$asArray) {
            $this->entries = json_decode($text, JSON_OBJECT_AS_ARRAY);
        }

        if ($this->isLoaded && $asArray) {

            $fn = function() use($registryFilename) {
               return include $registryFilename;
            };

            $dictionary = $fn();

            $this->entries = [];
            foreach($dictionary as $key => $value) {
                $this->entries[$key] = $value;
            }
        }

        return $this->isLoaded;
    }

    public function _getFlatFilename(): string
    {
        return $this->flatFilename ?: $this->flatFilename = strtolower(str_replace('\\', '_',  get_class($this)));
    }

    public function _getCacheFileName(bool $asArray = false): string
    {
        if ($this->cacheFilename === '') {
            $this->cacheFilename = $this->baseDirectory . $this->_getFlatFilename($asArray);
        }

        return $this->cacheFilename . ($asArray ? '.php' : '.json');
    }

    public function _setCacheDirectory(string $directory): void
    {
        $directory = substr($directory, -1) !== DIRECTORY_SEPARATOR ? $directory . DIRECTORY_SEPARATOR : $directory;
        $this->baseDirectory = $directory;
    }

    private function _shortClassName(): string
    {
        $fqname = get_class($this);
        $nameParts = explode('\\', $fqname);
        $basename = array_pop($nameParts);

        return $basename;
    }

}
