<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Middlewares\AttributeMiddlewareInterface;
use Ephect\Framework\Registry\MiddlewareRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentDeclarationInterface;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\UniqueCodeRegistry;
use Ephect\Modules\Forms\Generators\ComponentParser;
use Ephect\Modules\Forms\Generators\ParserService;
use ReflectionClass;

class UniqueComponentDescriptor implements DescriptorInterface
{
    public function __construct(protected string $buildDirectory)
    {
    }

    /**
     * @param string $sourceDir
     * @param string $filename
     * @return array|null[]
     * @throws \ReflectionException
     */
    public function describe(string $sourceDir, string $filename): array
    {

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

        include_once $sourceDir . $filename;

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
        $comp->load($sourceDir . $filename);

//        $fullCopyFilename = str_replace(\Constants::UNIQUE_DIR, \Constants::COPY_DIR, $fullFilename);

//        $parser = new ParserService($this->buildDirectory);
//        $parser->doEmptyComponents($comp);
//        if ($parser->getResult() === true) {
//            $html = $parser->getHtml();
//            File::safeWrite(\Constants::COPY_DIR . $filename, $html);
//            $comp->load(\Constants::COPY_DIR . $filename);
//        }

//        $comp->analyse();
//
        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();
        $declaration = new ComponentDeclaration($struct);

        $decl = [
            'uid' => Crypto::createOID(),
            'type' => 'function',
            'name' => $functionName,
            'arguments' => $arguments,
            'attributes' => $attributes,
            'returnType' => $refReturnType->getName(),
            'composition' => null, //$decl['composition'],
        ];

        //TODO: register middlewares if any
//        $this->registerMiddlewares($uid, $declaration);

        //TODO: register events if any

        UniqueCodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($filename, $comp->getUID());
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

//        $entity = ComponentEntity::buildFromArray($struct->composition);
//        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }

    /**
     * @throws \ReflectionException
     */
    public function registerMiddlewares(
        string $motherUID,
        ComponentDeclarationInterface $declaration,
    ): void {

        Logger::create()->dump(__METHOD__ . '::declaration', $declaration);

        /**
         * Mandatory test: Parent is not always null!
         */
        if (!$declaration->hasAttributes()) {
            return;
        }

        $attrs = $declaration->getAttributes();

        $middlewaresList = [];
        foreach ($attrs as $attr) {
            if (!isset($attr['name'])) {
                continue;
            }

            $attr = (object)$attr;

            if (count($attr->arguments) > 0) {
                $attrNew = new $attr->name(...$attr->arguments);
            } else {
                $attrNew = new $attr->name();
            }

            if ($attrNew instanceof AttributeMiddlewareInterface) {
                $middlewaresList[$attr->name] = [
                    $attr->arguments,
                    $attrNew->getMiddlewares(),
                ];
            }
        }

        if (count($middlewaresList)) {
            foreach ($middlewaresList as $key => $value) {
                [$arguments, $middlewares] = $value;
                foreach ($middlewares as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if ($middleware instanceof ComponentParserMiddlewareInterface) {
                        MiddlewareRegistry::write($middlewareClass, $arguments);
                    }

                    MiddlewareRegistry::save();
                }
            }
        }
    }

}
