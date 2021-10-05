<?php

namespace Ephect\Components\Generators;

interface ParserServiceInterface {
    public function getHtml(): string;
    public function getResult(): null|string|array|bool;
    public function getVariables(): ?array;
    public function getUses(): ?array;
}
