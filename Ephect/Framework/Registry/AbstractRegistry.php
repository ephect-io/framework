<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

abstract class AbstractRegistry implements RegistryInterface
{
    protected array $entries = [];
    protected bool $isLoaded = false;
    protected string $baseDirectory = CACHE_DIR;
    protected string $cacheFilename = '';
    protected string $flatFilename = '';

    use ElementTrait;

    public function _write(string $key, $value): void
    {
        $this->entries[$key] = $value;
    }

    public function _read($key, $value = null): mixed
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
        return isset($this->entries[$key]);
    }

    public function _save(bool $asArray = false): bool
    {
        $entries = $this->_items();

        $result = json_encode($entries, JSON_PRETTY_PRINT);

        if ($asArray) {
            $result = Text::jsonToPhpReturnedArray($result);
            $ephect_root = EPHECT_ROOT;
            if(DIRECTORY_SEPARATOR === '\\') {
                $ephect_root = str_replace('\\', '\\\\', EPHECT_ROOT);
            }

            $result = str_replace('"' . $ephect_root, 'EPHECT_ROOT . "', $result);
            $result = str_replace('"' . SRC_ROOT, 'SRC_ROOT . "', $result);
        }

        $registryFilename = $this->_getCacheFileName($asArray);
        $len = File::safeWrite($registryFilename, $result);

        return $len !== null;
    }

    public function _items(): array
    {
        return $this->entries;
    }

    public function _getCacheFileName(bool $asArray = false): string
    {
        if ($this->cacheFilename === '') {
            $this->cacheFilename = $this->baseDirectory . $this->_getFlatFilename($asArray);
        }

        return $this->cacheFilename . ($asArray ? '.php' : '.json');
    }

    public function _getFlatFilename(): string
    {
        return $this->flatFilename ?: $this->flatFilename = strtolower(str_replace('\\', '_', get_class($this)));
    }

    public function _load(bool $asArray = false): bool
    {
        $this->isLoaded = false;

        $registryFilename = $this->_getCacheFileName($asArray);

        $ok = is_file($registryFilename);

        if ($ok && !$asArray) {
            $text = file_get_contents($registryFilename);
            $this->entries = json_decode($text, JSON_OBJECT_AS_ARRAY);
            $this->isLoaded = true;
        }

        if ($ok && $asArray) {
            $this->entries = require $registryFilename;
            $this->isLoaded = true;
        }

        return $this->isLoaded;
    }

    public function _setCacheDirectory(string $directory): void
    {
        $directory = substr($directory, -1) !== DIRECTORY_SEPARATOR ? $directory . DIRECTORY_SEPARATOR : $directory;
        $this->baseDirectory = $directory;
    }

    protected function _shortClassName(): string
    {
        $fqname = get_class($this);
        $nameParts = explode('\\', $fqname);
        $basename = array_pop($nameParts);

        return $basename;
    }

}
