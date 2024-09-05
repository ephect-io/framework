<?php

namespace Ephect\Framework\Registry;

abstract class AbstractStaticRegistry extends AbstractRegistry implements StaticRegistryInterface, RegistryInterface
{
    use StaticRegistryTrait;
}
