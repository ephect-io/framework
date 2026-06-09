<?php

namespace Ephect\Modules\Http\Transport;

use Ephect\Modules\HttpStorage\Session\Session;

class History implements HistoryInterface
{
    private const HISTORY_KEY = 'history';
    private const LAST_REQUEST_KEY = 'last';
    private const LAST_GET_REQUEST_KEY = 'last-get';
    private Session $session;
    private ?RequestInfo $lastRequest = null;
    private ?RequestInfo $lastGetRequest = null;

    public function __construct()
    {
        $this->session = new Session();
        $this->session->start();
    }

    public function getLastRequest(): ?RequestInfo
    {
        return $this->lastRequest;
    }

    public function getLastGetRequest(): ?RequestInfo
    {
        return $this->lastGetRequest;
    }

    public function get(): array
    {
        return $this->session->read(self::HISTORY_KEY);
    }

    public function set(RequestInfo $info): void
    {
        $requests = $this->session->read(self::HISTORY_KEY) ?? [];
        $requests[] = $info;

        $this->session->write(self::HISTORY_KEY, $requests);

        if ($info->getMethod() == 'GET') {
            $getReq = $this->session->read(self::LAST_GET_REQUEST_KEY);
            $this->lastGetRequest = $getReq ?? $info;
            $this->session->write(self::LAST_GET_REQUEST_KEY, $info);
        }
        $req = $this->session->read(self::LAST_REQUEST_KEY);
        $this->lastRequest = $req ?? $info;

        $this->session->write(self::LAST_REQUEST_KEY, $info);

    }

    public function has(): bool
    {
        return $this->session->has(self::HISTORY_KEY);
    }

    public function clear(): void
    {
        $this->session->remove(self::HISTORY_KEY);
    }

}
