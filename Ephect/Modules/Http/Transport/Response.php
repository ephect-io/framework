<?php

namespace Ephect\Modules\Http\Transport;

class Response
{
    public function __construct(
        private ?string $content = '',
        private HttpStatusCodeEnum|int $status = 200,
        private array $headers = [],
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatus(): int|HttpStatusCodeEnum
    {
        $status = $this->status;
        if ($this->status instanceof HttpStatusCodeEnum) {
            $status = $this->status->value;
        }
        return $status;
    }

    public function setStatus(int|HttpStatusCodeEnum $status): void
    {
        $this->status = $status;
    }

    public function send(): void
    {
        ob_start();

        foreach ($this->buildHeaders() as $header) {
            header($header);
        }

        echo $this->content;

        ob_end_flush();
    }

    protected function buildHeaders(): array
    {
        $result = [];
        foreach ($this->headers as $key => $value) {
            $result[] = "$key: " . $value;
        }

        return $result;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(string $key, mixed $value): void
    {
        $this->headers[$key] = $value;
    }

    /**
     * @throws Exception
     */
    protected function buildHeader(string $header): string
    {
        $result = '';
        foreach ($this->headers as $key => $value) {
            if ($key == $header) {
                $result = "$key: " . $value;
                break;
            }
        }

        if ($result == '') {
            throw new Exception('Header not found!');
        }

        return $result;
    }
}