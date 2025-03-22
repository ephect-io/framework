<?php

namespace Ephect\Modules\Forms\Generators;

interface ParserServiceInterface
{
    public function getHtml(): string;

    public function getResult(): null|string|array|bool;

    public function getFuncVariables(): ?array;

    public function getUseVariables(): ?array;

    public function getUses(): ?array;
}
