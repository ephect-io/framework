<?php

namespace Ephect\Modules\Http\Transport;

class RequestInfo implements RequestInfoInterface
{
    private array $getParams;
    private array $postParams;
    private array $cookies;
    private array $files;
    private array $server;
    private string $pathInfo;
    private string $method;
    private string $body;
    public function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->pathInfo = strtok($this->server['REQUEST_URI'], '?');
        $this->method = $this->server['REQUEST_METHOD'];
        $this->body = file_get_contents('php://input');
    }

    public function getGetParams(string $param = ''): array|string
    {
        if ($param == '') {
            return $this->getParams;
        }

        return !isset($this->getParams[$param]) ? '' : $this->getParams[$param];
    }

    public function getPostParams(string $param = ''): array|string
    {
        if ($param == '') {
            return $this->postParams;
        }

        return !isset($this->postParams[$param]) ? '' : $this->postParams[$param];
    }

    public function getCookies($name = ''): array|string
    {
        if ($name == '') {
            return $this->cookies;
        }

        return !isset($this->cookies[$name]) ? '' : $this->cookies[$name];
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getServer(): array
    {
        return $this->server;
    }

    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
