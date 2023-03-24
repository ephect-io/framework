<?php

namespace Ephect\Framework\Components;

interface WebComponentInterface extends FileComponentInterface
{
    static public function htmlToScript($html): string;
}
