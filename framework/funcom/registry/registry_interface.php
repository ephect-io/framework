<?php
namespace FunCom\Registry;

interface RegistryInterface
{
    static function getInstance(): RegistryInterface;
    static function write(string $key, $item): void;
    static function read($key, $item = null);
    static function items();
    static function cache();
}