<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\IO\Utils;
use Exception;
use function Ephect\Hooks\useEffect;

class Builder
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
     * @param string $entrypoint
     * @param array $arguments
     * @param string $srcDir
     * @param string $destDir
     * @return void
     */
    function copyTemplates(string $tagName, string $className, bool $hasBackendProps, string $entrypoint, array $arguments, string $srcDir, string $destDir): void
    {

        $classText = Utils::safeRead($srcDir . 'Base.class.mjs');
        $classText = str_replace('Base', $className, $classText);
        $classText = str_replace('entrypoint', $entrypoint, $classText);

        $componentText = Utils::safeRead($srcDir . 'Base.tpl');
        $componentText = str_replace('Base', $className, $componentText);
        $componentText = str_replace('tag-name', $tagName, $componentText);
        $componentText = str_replace('entrypoint', $entrypoint, $componentText);

        $baseElementText =   Utils::safeRead($srcDir . 'BaseElement.js');
        $baseElementText = str_replace('Base', $className, $baseElementText);

        $parameters = $arguments;
        $arguments[] = 'styles';
        $arguments[] = 'classes';

        if (count($arguments) == 0) {
            $classText = str_replace('(DeclaredAttributes)', "()", $classText);

            $baseElementText = str_replace('GetAttributes', '', $baseElementText);
            $componentText = str_replace('<Attributes />', '', $componentText);

            Utils::safeWrite($destDir . "$className.class.mjs", $classText);
            Utils::safeWrite($destDir . "$className.phtml", $componentText);
            Utils::safeWrite($destDir . $className . "Element.js", $baseElementText);

            return;
        }

        $properties = '';
        foreach ($arguments as $property) {
            $properties .= <<< HTML
                this.$property\n
                HTML;
            $properties .= '            ';
        }

        $baseElementText = str_replace('Properties', $properties, $baseElementText);

        $attributes = array_map(function ($item) {
            return "'$item'";
        }, $arguments);

        $thisParameters = array_map(function ($item) {
            return "this." . $item;
        }, $parameters);

        $declaredAttributes = implode(", ", $parameters);
        $attributes = implode(", ", $attributes);

        $argumentListAndResult = $thisParameters;
        $argumentListAndResult[] = "result";
//        $attributeListAndResult = implode(", ", $argumentListAndResult);
        $thisAttributeList = implode(", ", $thisParameters);

        $observeAttributes = <<< HTML
                static get observeAttributes() {
                        /**
                        * Attributes passed inline to the component
                        */
                        return [$attributes]
                    }
            HTML;

        $baseElementText = str_replace('ObserveAttributes', $observeAttributes, $baseElementText);

        $getAttributes = '';
        foreach ($arguments as $attribute) {
            $getAttributes .= <<< HTML
                    get $attribute() {
                            return this.getAttribute('$attribute') ?? null
                        }\n
                HTML;
            $getAttributes .= '    ';
        }

        $classText = str_replace('(DeclaredAttributes)', "(" . $declaredAttributes . ")", $classText);

        $baseElementText = str_replace('GetAttributes', $getAttributes, $baseElementText);
        $componentText = str_replace('AttributeList', $thisAttributeList, $componentText);
//        $componentText = str_replace('<AttributeListAndResult />', $attributeListAndResult, $componentText);

        Utils::safeWrite($destDir . "$className.class.mjs", $classText);
        Utils::safeWrite($destDir . $className . "Element.js", $baseElementText);

        if ($hasBackendProps) {
            $namespace = CONFIG_NAMESPACE;

            $componentText = str_replace("</template>", "    <h2>{{ foo }}</h2>\n</template>", $componentText);
            $componentText = <<< COMPONENT
            <?php
            namespace $namespace;

            use function Ephect\Hooks\useEffect;

            function $className(\$slot) {

            useEffect(function (\$slot, /* string */ \$foo) {
                \$foo = "It works!"; 
            });

            return (<<< HTML
            <WebComponent>
            $componentText
            </WebComponent>
            HTML);
            }
            
            COMPONENT;
        }

        Utils::safeWrite($destDir . "$className.phtml", $componentText);

    }
}
