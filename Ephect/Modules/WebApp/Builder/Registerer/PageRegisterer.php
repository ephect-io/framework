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
            $uid = Crypto::createOID();
            $filename = \Constants::CUSTOM_PAGES_ROOT . $page;

            [
                $namespace,
                $functionName,
                $parameters,
                $returnedType,
                $startsAt
            ] = ElementUtils::getFunctionDefinitionFromFile($filename);

            if ($startsAt == -1) {
                $info = (object)pathinfo($filename);
                $namespace = \Constants::CONFIG_NAMESPACE;
                $functionName = $info->filename;
            }

            $className = $namespace . '\\' . $functionName;
            PageRegistry::write($uid, $className);
            PageRegistry::write($className, $page);
            PageRegistry::write($page, $uid);
        }
        PageRegistry::save();
    }
}
