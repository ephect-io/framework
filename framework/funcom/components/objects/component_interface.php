<?php

namespace FunCom\Components;

interface ComponentInterface
{
    public function getCode();
    public function getFullCleasName(): string;
    public function getNamespace(): string;
    public function getFunction(): string;
}
