<?php

namespace Ephect\WebApp\Web;

use Ephect\WebApp\Web\Request\Headers;

readonly class Request
{

    public Headers $headers;
    public string $body;
    public ?array $parameters;
    public string $method;
    public string $uri;

    public function __construct()
    {
        parse_str(QUERY_STRING, $params);

        $this->headers = new Headers();
        $this->body = file_get_contents('php://input');
        $this->method = REQUEST_METHOD;
        $this->uri = REQUEST_URI;
        $this->parameters = $params;

    }
}