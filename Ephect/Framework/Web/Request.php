<?php

namespace Ephect\Framework\Web;

use Ephect\Framework\Web\Request\Headers;

readonly class Request {

    public Headers $headers;
    public string $body;
    public ?array $parameters;
    public string $method;
    public string $uri;

    public function __construct() {
        $this->headers = new Headers();
        $this->body = file_get_contents('php://input');
        $this->parameters = QUERY_STRING;
        $this->method = REQUEST_METHOD;
        $this->uri = REQUEST_URI;
    }
}