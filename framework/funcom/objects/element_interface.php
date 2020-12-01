<?php

namespace FunCom;

interface ElementInterface
{
    function getUID(): string;
    function getId(): string;
    function getParent(): ?ElementInterface;
    function getType(): string;
}