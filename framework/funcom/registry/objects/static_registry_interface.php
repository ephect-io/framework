<?php
namespace FunCom\Registry;

interface StaticRegistryInterface
{
    static function getInstance(): StaticRegistryInterface;
    static function write(string $key, $item): void;
    static function read($key, $item = null);
    static function items();
    static function cache();
    static function uncache();
    static function exists();
}