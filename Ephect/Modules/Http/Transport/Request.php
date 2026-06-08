<?php

namespace Ephect\Modules\Http\Transport;

use Ephect\Framework\Session\Session;

class Request implements RequestInfoInterface
{
    private RequestInfo $info;
    private Session $session;
    public RequestHeaders $headers;

    public function __construct()
    {
        $this->info = new RequestInfo();
        parse_str(\Constants::QUERY_STRING, $params);

        $this->headers = new RequestHeaders();
        $this->session = new Session();
    }

    public function getGetParams(string $param = ''): array|string
    {
        return $this->info->getGetParams($param);
    }

    public function getPostParams(string $param = ''): array|string
    {
        return $this->info->getPostParams($param);
    }

    public function getCookies($name = ''): array|string
    {
        return $this->info->getCookies($name);
    }

    public function getFiles(): array
    {
        return $this->info->getFiles();
    }

    public function getServer(): array
    {
        return $this->info->getServer();
    }

    public function getPathInfo(): string
    {
        return $this->info->getPathInfo();
    }

    public function getMethod(): string
    {
        return $this->info->getMethod();
    }

    public function getBody(): string
    {
        return $this->info->getBody();
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }
}
