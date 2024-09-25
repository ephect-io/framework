<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Components\ComponentDeclarationStructure;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Forms\Generators\ComponentParser;
use Forms\Generators\ParserService;
use ReflectionClass;

class UniqueComponentDescriptor implements DescriptorInterface
{
    public function describe(string $sourceDir, string $filename): array
    {
        [
            $namespace,
            $functionName,
            $parameters,
            $returnedType,
            $startsAt
        ] = ElementUtils::getFunctionDefinitionFromFile($filename);

        include_once $filename;

        $ref = new \ReflectionFunction($functionName);
        $parameters = $ref->getParameters();
        $attributes = $ref->getAttributes();
        $returnType = $ref->getReturnType();

//        $decl = [
//            'uid' => $uid,
//            'type' => $func[0],
//            'name' => $func[1],
//            'arguments' => $func[2],
//            'attributes' => $attrs,
//            'composition' => $this->list
//        ];
//
//        $struct = new ComponentDeclarationStructure($decl);

        $comp = new Component();
        $comp->load($filename);

        $parser = new ParserService();
        $parser->doEmptyComponents($comp);
        if ($parser->getResult() === true) {
            $html = $parser->getHtml();
            File::safeWrite(COPY_DIR . $filename, $html);
            $comp->load($filename);
        }

        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($filename, $uid);
        ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        return [$comp->getFullyQualifiedFunction(), $comp];
    }
}