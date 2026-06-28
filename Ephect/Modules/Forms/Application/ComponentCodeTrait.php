<?php

namespace Ephect\Modules\Forms\Application;

trait ComponentCodeTrait
{
    protected ?string $filename = '';
    protected ?string $code = '';
    protected int $bodyStartsAt = 0;
    protected ?array $arguments = null;
    protected ?string $returnType = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }

    public function getReturnType(): ?string
    {
        return $this->returnType;
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
