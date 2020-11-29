<?php

namespace FunCom\Core;

interface ElementInterface
{
    function getUID(): string;
    function getId(): string;
    function getParent(): ?ElementInterface;
    // function setParent(ElementInterface $parent) : void;
    function getType(): string;
}