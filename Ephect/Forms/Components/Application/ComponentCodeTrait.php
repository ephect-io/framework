<?php

namespace Ephect\Forms\Components\Application;

trait ComponentCodeTrait
{
    protected ?string $filename = '';
    protected ?string $code = '';
    protected int $bodyStartsAt = 0;

    public function getCode(): ?string
    {
        return $this->code;
    }

    function applyCode(string $code): void
    {
        $this->code = $code;
    }

    public function getBodyStart(): int
    {
        return $this->bodyStartsAt;
    }
}