<?php

namespace Ephect\Modules\Forms\Application;

trait ComponentCodeTrait
{
    protected ?string $filename = '';
    protected ?string $code = '';
    protected int $bodyStartsAt = 0;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function applyCode(string $code): void
    {
        $this->code = $code;
    }

    public function getBodyStart(): int
    {
        return $this->bodyStartsAt;
    }
}