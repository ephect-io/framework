<?php

namespace Ephect\Framework\Templates;

use Ephect\Framework\Utils\File;

class TemplateMaker
{
    private string $template;

    public function __construct(private readonly string $filename)
    {
        $contents = File::safeRead($this->filename);
        $this->template = ($contents !== null) ? $contents : '';
    }

    public function make(array $params): void
    {
        $keys = array_keys($params);

        if (count($keys) == 0) {
            return;
        }

        foreach ($keys as $key) {
            $this->template = str_replace('{{' . $key . '}}', $params[$key], $this->template);
        }
    }

    public function save(string $destinationFile): void
    {
        File::safeWrite($destinationFile, $this->template);
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

}
