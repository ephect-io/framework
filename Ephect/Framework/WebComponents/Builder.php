<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\IO\Utils;
use Exception;

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

        if (!Utils::safeMkDir($destDir)) {
            throw new Exception("$destDir creation failed");
        }

        $destDir = realpath($destDir);

        $struct = new ManifestStructure([
            'tag' => $tagName,
            'class' => $className,
            'entrypoint' => $entrypoint,
            'arguments' => $arguments,
        ]);
        $json = json_encode($struct->toArray(), JSON_PRETTY_PRINT);

        $destDir .= DIRECTORY_SEPARATOR;

        Utils::safeWrite($destDir . DIRECTORY_SEPARATOR . 'manifest.json', $json);

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
    function copyTemplates(string $tagName, string $className, string $entrypoint, array $arguments, string $srcDir, string $destDir): void
    {

        $classText = Utils::safeRead($srcDir . 'Base.class.mjs');
        $classText = str_replace('Base', $className, $classText);
        $classText = str_replace('entrypoint', $entrypoint, $classText);
        Utils::safeWrite($destDir . "$className.class.mjs", $classText);

        $componentText = Utils::safeRead($srcDir . 'Base.component.phtml');
        $componentText = str_replace('Base', $className, $componentText);
        $componentText = str_replace('<TagName />', $tagName, $componentText);
        $componentText = str_replace('<Entrypoint />', $entrypoint, $componentText);

        if(count($arguments) > 0) {
            $properties = '';
            foreach($arguments as $property) {
                $properties .= <<< HTML
                    this.$property\n
                HTML;
                $properties .= '            ';
            }

            $componentText = str_replace('<Properties />', $properties, $componentText);

            $attributes = array_map(function($item) {
                return "'$item'";
            }, $arguments);

            $attributes = implode(", ", $attributes);

            $observeAttributes = <<< HTML
            static get observeAttributes() {
                        /**
                        * Attributes passed inline to the component
                        */
                        return [$attributes]
                    }
            HTML;

            $componentText = str_replace('<ObserveAttributes />', $observeAttributes, $componentText);

            $getAttributes = '';
            foreach($arguments as $attribute) {
                $getAttributes .= <<< HTML
                get $attribute() {
                            return this.getAttribute('$attribute') ?? null
                        }\n
                HTML;
                $getAttributes .= '        ';

            }

            $componentText = str_replace('<GetAttributes />', $getAttributes, $componentText);

        }

        Utils::safeWrite($destDir . "$className.component.phtml", $componentText);

        copy($srcDir . 'Base.tpl', $destDir . "$className.tpl");
    }
}