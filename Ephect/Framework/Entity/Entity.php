<?php

namespace Ephect\Framework\Entity;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Core\StructureInterface;
use Ephect\Framework\ElementInterface;
use Ephect\Framework\ElementTrait;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class Entity implements ElementInterface
{

    use ElementTrait;

    protected string $filename = '';
    protected array $data = [];

    public function __construct(protected ?StructureInterface $structure = null)
    {
    }

    public static function create(?StructureInterface $struct = null): ElementInterface
    {
        return new self($struct);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @throws \ErrorException
     */
    public function load(bool $asPhpArray = false): void
    {
        $json = [];
        $info = pathinfo($this->filename);

        if($asPhpArray) {
            $filename = $info['dirname']  . DIRECTORY_SEPARATOR .  $info['filename'] .  ".php";
            if(!file_exists($filename)) {
                throw new \ErrorException("File '$filename' not found");
            }
            $json = require $filename;
        } else {
            $filename = $info['dirname']  . DIRECTORY_SEPARATOR .  $info['filename'] .  ".json";
            if(!file_exists($filename)) {
                throw new \ErrorException("File '$filename' not found");
            }
            $json = file_get_contents($filename);
            $json = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        $this->data = $json;
    }

    public function save(bool $asPhpArray = false): void
    {
        $array = $this->structure->toArray();
        $info = pathinfo($this->filename);

        if($asPhpArray) {
            $filename = $info['dirname']  . DIRECTORY_SEPARATOR .  $info['filename'] .  ".php";
            $phpArray = Text::jsonToPhpReturnedArray($array);
            File::safeWrite($filename, $phpArray);
        } else {
            $filename = $info['dirname']  . DIRECTORY_SEPARATOR .  $info['filename'] .  ".json";
            $json = json_encode($array);
            File::safeWrite($filename, $json);
        }
    }
}