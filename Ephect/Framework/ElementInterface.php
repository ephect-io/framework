<?php

namespace Ephect\Framework;

interface ElementInterface
{
    public static function functionName($fullQualifiedName): string;

    public function getUID(): string;

    public function getMotherUID(): string;

    public function getId(): string;

    public function getParent(): ?ElementInterface;

    public function getClass(): string;

    public function getBaseClass(): string;

    public function getNamespace(): string;

}
