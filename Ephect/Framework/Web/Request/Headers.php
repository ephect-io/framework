<?php

namespace Ephect\Framework\Web\Request;

readonly class Headers {

    public array $list;

    public function __construct() {
        $this->list = array_change_key_case(getallheaders(), CASE_LOWER);
    }

    /**
     * Search the needle string in headers values using headers key
     *
     * @param $needle
     * @param $key
     * @return boolean
     */
    public function contains($needle, $key):bool
    {
        return strpos($this->list[$key], $needle) > -1;
    }
}