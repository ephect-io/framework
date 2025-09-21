<?php

namespace Ephect\Modules\WebComponent\Builder;

use Ephect\Framework\Templates\TemplateMaker;
use Ephect\Modules\WebComponent\Common;
use Ephect\Modules\WebComponent\Manifest\ManifestStructure;
use Ephect\Modules\WebComponent\Manifest\ManifestWriter;
use Exception;

class Compiler
{

    /**
     * Second creation step of the WebComponent
     *
     * Create a manifest file include all details passed to the command line
     *
     * @param string $tagName
     * @param string $className
     * @param string $entrypoint
     * @param array $arguments
     * @param string $destDir
     * @return void
     * @throws Exception
     */
    function saveManifest(string $tagName, string $className, string $entrypoint, array $arguments, string $destDir): void
    {

        $struct = new ManifestStructure([
            'tag' => $tagName,
            'class' => $className,
            'entrypoint' => $entrypoint,
            'arguments' => $arguments,
        ]);

        $writer = new ManifestWriter($struct, $destDir);
        $writer->write();
    }

    /**
     * Third and last creation step of the WebComponent
     *
     * Read templates text, replace the markups and save into user application directory
     *
     * @param string $tagName
     * @param string $className
     * @param bool $hasBackendProps
     * @param string $entrypoint
     * @param array $arguments
     * @param string $srcDir
     * @param string $destDir
     * @return void
     */
    function copyTemplates(
        string $tagName,
        string $className,
        bool $hasBackendProps,
        string $entrypoint,
        array $arguments,
        string $srcDir,
        string $destDir
    ): void {
        $common = new Common();
        $templatesDir = $common->getModuleSrcDir() . 'Templates' . DIRECTORY_SEPARATOR;

        $classTextMaker = new TemplateMaker($templatesDir . 'Base.class.tpl');
        $baseElementTextMaker = new TemplateMaker($templatesDir . 'BaseElement.tpl');
        $baseTextMaker = new TemplateMaker($templatesDir . 'Base.tpl');
        $componentTextMaker = new TemplateMaker($templatesDir . 'Component.tpl');

        $classTextMaker->make(['Base' => $className, 'entrypoint' => $entrypoint,]);

        $baseTextMaker->make([
            'Base' => $className,
            'tag-name' => $tagName,
            'entrypoint' => $entrypoint,
            'objectName' => lcfirst($className),
        ]);

        $baseElementTextMaker->make(['Base' => $className,]);

        $parameters = $arguments;
        $arguments[] = 'styles';
        $arguments[] = 'classes';

        if (count($arguments) == 0) {
            $classTextMaker->make(['DeclaredAttributes' => '',]);
            $baseElementTextMaker->make(['GetAttributes' => '',]);
            $baseTextMaker->make(['Attributes' => '',]);

            $classTextMaker->save($destDir . "$className.class.js");
            $baseElementTextMaker->save($destDir . $className . "Element.js");
            $baseTextMaker->save($destDir . "$className.phtml");

            return;
        }

        $properties = '';
        foreach ($arguments as $property) {
            $properties .= <<< HTML
                this.$property\n
                HTML;
            $properties .= '            ';
        }

        $baseElementTextMaker->make(['Properties' => $properties,]);

        $attributes = array_map(function ($item) {
            return "'$item'";
        }, $arguments);

        $thisParameters = array_map(function ($item) {
            return "this." . $item;
        }, $parameters);

        $declaredAttributes = implode(", ", $parameters);
        $attributes = implode(", ", $attributes);

        $thisAttributeList = implode(", ", $thisParameters);

        $observeAttributes = <<< HTML
                static get observeAttributes() {
                        /**
                        * Attributes passed inline to the component
                        */
                        return [$attributes]
                    }
            HTML;

        $baseElementTextMaker->make(['ObserveAttributes' => $observeAttributes,]);

        $getAttributes = '';
        foreach ($arguments as $attribute) {
            $getAttributes .= <<< HTML
                    get $attribute() {
                            return this.getAttribute('$attribute') ?? null
                        }\n
                HTML;
            $getAttributes .= '    ';
        }

        $classTextMaker->make(['DeclaredAttributes' => $declaredAttributes,]);
        $baseElementTextMaker->make(['GetAttributes' => $getAttributes,]);
        $baseTextMaker->make(['AttributeList' => $thisAttributeList,]);

        if ($hasBackendProps) {
            $namespace = \Constants::CONFIG_NAMESPACE;

            $baseTextMaker->make(['endTemplate' => '<h2>{{ foo }}</h2>',]);

            $funcBody = <<< FUNC_BODY
            useEffect(function (\$slot, /* string */ \$foo) {
                \$foo = "It works!"; 
            });
            FUNC_BODY;

            $componentTextMaker->make([
                'funcNamespace' => $namespace,
                'funcName' => $className,
                'funcBody' => $funcBody,
                'html' => $baseTextMaker->getTemplate(),
            ]);
            $baseTextMaker->setTemplate($componentTextMaker->getTemplate());
        } else {
            $baseTextMaker->make(['endTemplate' => '',]);
        }

        $classTextMaker->save($destDir . $className . \Constants::CLASS_JS_EXTENSION);
        $baseElementTextMaker->save($destDir . $className . "Element" . \Constants::JS_EXTENSION);
        $baseTextMaker->save($destDir . "$className.phtml");
    }
}
