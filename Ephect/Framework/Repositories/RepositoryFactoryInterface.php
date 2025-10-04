<?php

namespace Ephect\Framework\Repositories;

interface RepositoryFactoryInterface
{
    public function create(string $repositoryClass): RepositoryInterface|null;

}
