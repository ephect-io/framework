<?php

namespace Ephect\Modules\DataAccess;

readonly class SqlParameters
{
    public function __construct(
        public string $Host = '',
        public string $User = '',
        public string $Password = '',
        public string $DatabaseName = '',
        public string $ServerType = ServerType::SQLITE,
    )
    {
    }

}
