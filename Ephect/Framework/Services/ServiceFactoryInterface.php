<?php

namespace Ephect\Framework\Services;

interface ServiceFactoryInterface
{
    public function create(string $serviceClass): ServiceInterface|null;

}
