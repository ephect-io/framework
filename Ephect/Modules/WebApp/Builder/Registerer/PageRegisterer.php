<?php

namespace Ephect\Modules\WebApp\Builder\Registerer;

use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\ElementUtils;
use Ephect\Modules\WebApp\Registry\PageRegistry;

class PageRegisterer implements RegistererInterface
{
    public function register(array $values): void
    {
        PageRegistry::load();
        foreach ($values as $page) {
            $uid = Crypto::createUID();
            $filename = \Constants::CUSTOM_PAGES_ROOT . $page . \Constants::PREHTML_EXTENSION;

            [
                $namespace,
                $functionName,
                $parameters,
                $returnedType,
                $startsAt
            ] = ElementUtils::getFunctionDefinitionFromFile($filename);

            $className = $namespace . '\\' . $functionName;
            PageRegistry::write($uid, $className);
            PageRegistry::write($className, $filename);
            PageRegistry::write($filename, $uid);
        }
        PageRegistry::save();
    }
}