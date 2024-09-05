<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\ElementInterface;

interface RouteInterface extends ElementInterface
{
    public function getMethod(): string;

    public function getRule(): string;

    public function getNormalized(): string;

    public function getRedirect(): string;

    public function getTranslation(): string;

    public function getError(): int;

    public function isExact(): bool;

    public function getMiddlewares(): array;
}