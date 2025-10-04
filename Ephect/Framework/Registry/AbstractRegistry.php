<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

abstract class AbstractRegistry implements RegistryInterface
{
    use ElementTrait;

    protected array $entries = [];
    protected bool $isLoaded = false;
    protected string $baseDirectory = \Constants::BUILD_DIR;
    protected string $cacheFilename = '';
    protected string $flatFilename = '';

    protected string $buildDirectory = \Constants::BUILD_DIR;

    public function __write(string $key, $value): void
    {
        $this->entries[$key] = $value;
    }

    public function __read($key, $value = null): mixed
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

    public function __delete(string $key): void
    {
        unset($this->entries[$key]);
    }

    public function __exists(string $key): bool
    {
        return isset($this->entries[$key]);
    }

    public function __save(bool $asArray = false): bool
    {
        $entries = $this->__items();

        $result = json_encode($entries, JSON_PRETTY_PRINT);

        if ($asArray) {
            $result = Text::jsonToPhpReturnedArray($result);
            $ephect_root = \Constants::EPHECT_ROOT;
            if (DIRECTORY_SEPARATOR === '\\') {
                $ephect_root = str_replace('\\', '\\\\', \Constants::EPHECT_ROOT);
            }

            $result = str_replace('"' . $ephect_root, 'EPHECT_ROOT . "', $result);
            $result = str_replace('"' . \Constants::SRC_ROOT, 'SRC_ROOT . "', $result);
        }

        $registryFilename = $this->__getCacheFileName($asArray);
        $len = File::safeWrite($registryFilename, $result);

        return $len !== null;
    }

    public function __items(): array
    {
        return $this->entries;
    }

    public function __getCacheFileName(bool $asArray = false): string
    {
        $this->cacheFilename = $this->baseDirectory . $this->__getFlatFilename($asArray);

        return $this->cacheFilename . ($asArray ? '.php' : '.json');
    }

    public function __getFlatFilename(): string
    {
        return $this->flatFilename ?: $this->flatFilename = strtolower(str_replace('\\', '_', get_class($this)));
    }

    public function __load(bool $asArray = false): bool
    {
        $this->isLoaded = false;

        $registryFilename = $this->__getCacheFileName($asArray);

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

    public function __setCacheDirectory(string $directory): void
    {
        $directory = substr($directory, -1) !== DIRECTORY_SEPARATOR ? $directory . DIRECTORY_SEPARATOR : $directory;
        $this->baseDirectory = $directory;
    }

    protected function __shortClassName(): string
    {
        $fqname = get_class($this);
        $nameParts = explode('\\', $fqname);
        $basename = array_pop($nameParts);

        return $basename;
    }
}
