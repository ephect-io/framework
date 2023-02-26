<?php

namespace Ephect\Commands\CreateWebcomponent;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Webcomponents\ManifestStructure;
use Exception;

class Lib
{

    public function createWebcomponentBase(): void
    {
        try {

            Console::writeLine(ConsoleColors::getColoredString("Creating a new webcomponent.", ConsoleColors::LIGHT_BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Please, answer the following questions.", ConsoleColors::BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Leave the answer blank to pass to the next question or to abort the process.", ConsoleColors::BROWN));

            [$tagName, $className, $entrypoint, $arguments] = $this->readLine();

            $destDir = SRC_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . $className . DIRECTORY_SEPARATOR;

            $this->saveManifest($tagName, $className, $entrypoint, $arguments, $destDir);

            $srcDir = EPHECT_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

            $this->copyTemplates($tagName, $className, $entrypoint, $arguments, $srcDir, $destDir);

            Console::writeLine(ConsoleColors::getColoredString("Webcomponent ", ConsoleColors::BLUE) . "%s" .  ConsoleColors::getColoredString(" is available in:", ConsoleColors::BLUE), $className);
            Console::writeLine("%s", $destDir);
        } catch (Exception $ex) {
            Console::error($ex);
        }
    }

    /**
     * Ask some questions to get the properties of the webcomponent
     *
     * @return void
     */
    function readLine()
    {
        /**
         * Asking the tag name
         */
        $tagName = Console::readLine("Tag name (kebab-case):");
        $tagName =  strtolower($tagName);
        if (trim($tagName) == '') {
            throw new Exception("Webcomponent tag name must not be empty");
        }

        Console::writeLine(ConsoleColors::getColoredString("The code of the webcomponent will split into one JS module and one HTML template.", ConsoleColors::BLUE));

        /**
         * Asking for the class name
         */
        $className = Console::readLine("Module class name (PascalCase):");
        if (trim($className) == '') {
            throw new Exception("Webcomponent class name must not be empty");
        }

        /**
         * Asking for entrypoint
         */
        $entrypoint = Console::readLine("Entrypoint in class (camelCase):");
        if (trim($entrypoint) == '') {
            throw new Exception("Webcomponent entrypoint must not be empty");
        }

        /**
         * Asking for arguments
         */
        $next = true;
        $argIndex = 1;
        $arguments = [];
        while ($next) {
            $arg = Console::readLine("Argument $argIndex:");
            if (trim($arg) == '') {
                Console::writeLine(ConsoleColors::getColoredString("Ending Webcomponent arguments list", ConsoleColors::LIGHT_BLUE));
                $next = false;
                continue;
            }
            array_push($arguments, $arg);
            $argIndex++;
        }

        return [$tagName, $className, $entrypoint, $arguments];
    }

    /**
     * Second creation step of the webcomponent
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
    function saveManifest(string $tagName, string $className, string $entrypoint, array $arguments, string $destDir) {

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
     * Third and last creation step of the webcomponent
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
    function copyTemplates(string $tagName, string $className, string $entrypoint, array $arguments, string $srcDir, string $destDir)
    {

        $classText = Utils::safeRead($srcDir . 'Base.class.mjs');
        $classText = str_replace('Base', $className, $classText);
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
