<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\UniqueCodeRegistry;
use Ephect\Modules\Forms\Generators\ComponentParser;
use Ephect\Modules\Forms\Generators\ParserService;
use ReflectionClass;

class UniqueComponentDescriptor implements DescriptorInterface
{
    /**
     * @param string $sourceDir
     * @param string $filename
     * @return array|null[]
     * @throws \ReflectionException
     */
    public function describe(string $sourceDir, string $filename): array
    {
        $fullFilename = $sourceDir . $filename;

        [
            $namespace,
            $functionName,
            $parameters,
            $returnedType,
            $startsAt
        ] = ElementUtils::getFunctionDefinitionFromFile($sourceDir . $filename);

        if ($functionName == '') {
            return [null, null];
        }

        include_once $fullFilename;

        $fqFuncName = $namespace . '\\' . $functionName;

        $ref = new \ReflectionFunction($fqFuncName);
        $refParameters = $ref->getParameters();
        $refAttributes = $ref->getAttributes();
        $refReturnType = $ref->getReturnType();

        $arguments = array_map(function ($parameter) {
            return $parameter->getName();
        }, $refParameters);

        $attributes = array_map(function ($attribute) {
            return [
                'name' => get_class($attribute->newInstance()),
                'arguments' => $attribute->getArguments(),
            ];
        }, $refAttributes);

        $comp = new Component();
        $comp->load($fullFilename);

        $fullCopyFilename = str_replace(\Constants::UNIQUE_DIR, \Constants::COPY_DIR, $fullFilename);

        $parser = new ParserService();
        $parser->doEmptyComponents($comp);
        if ($parser->getResult() === true) {
            $html = $parser->getHtml();
            File::safeWrite($fullCopyFilename, $html);
            $comp->load($fullCopyFilename);
        }

        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        $decl = [
            'uid' => Crypto::createOID(),
            'type' => 'function',
            'name' => $functionName,
            'arguments' => $arguments,
            'attributes' => $attributes,
            'returnType' => $refReturnType->getName(),
            'composition' => $decl['composition'],
        ];

        UniqueCodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($filename, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}
