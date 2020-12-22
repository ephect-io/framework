<?php

namespace FunCom\Cache;

use FunCom\IO\Utils;

class Cache 
{
     public function delete(): void
     {
        Utils::delTree(CACHE_DIR);
     }
}