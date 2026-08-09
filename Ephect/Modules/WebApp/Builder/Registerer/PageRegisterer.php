<?php

namespace Ephect\Modules\WebApp\Builder\Registerer;

use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\ElementUtils;
use Ephect\Modules\WebApp\Registry\PageRegistry;

use function Ephect\Hooks\useMemory;

class PageRegisterer implements RegistererInterface
{
    public function register(array $values): void
    {
        PageRegistry::load();
        $pages = [];
        foreach ($values as $page) {
            $uid = Crypto::createOID();
            $filename = \Constants::COPY_PAGES_ROOT . $page;

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
            PageRegistry::write($functionName, $page);
            PageRegistry::write($uid, $className);
            PageRegistry::write($className, $page);
            PageRegistry::write($page, $uid);
            $pages[] = [
                $functionName => [
                    'uid' => $uid,
                    'className' => $className,
                    'filename' => $page,
                    'parameters' => $parameters,
                    'returnedType' => $returnedType,
                    'startsAt' => $startsAt
                ]
            ];
        }
        useMemory(['pages'  => $pages]);

        PageRegistry::save();
    }
}
