<?php

namespace Ephect\Commands\CreateWebcomponent;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Webcomponents\ManifestStructure;

class Lib
{

    public function createWebcomponentBase(): void
    {
        Console::writeLine(ConsoleColors::getColoredString("Creating a new webcomponent.", ConsoleColors::LIGHT_BLUE));
        Console::writeLine(ConsoleColors::getColoredString("Please, answer the following questions.", ConsoleColors::BLUE));
        Console::writeLine(ConsoleColors::getColoredString("Leave the answer blank to pass to the next question or to abort the process.", ConsoleColors::BROWN));

        /**
         * Asking the tag name
         */
        $tagName = Console::readLine("Tag name (kebab-case):");
        $tagName =  strtolower($tagName);
        if(trim($tagName) == '')  {
            Console::writeLine(ConsoleColors::getColoredString("FATAL: Webcomponent tag name must not be empty", ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
            return;
        }

        Console::writeLine(ConsoleColors::getColoredString("The code of the webcomponent will split into one JS module and one HTML template.", ConsoleColors::BLUE));

        /**
         * Asking for the class name
         */
        $className = Console::readLine("Module class name (PascalCase):");
        if(trim($className) == '')  {
            Console::writeLine(ConsoleColors::getColoredString("FATAL: Webcomponent class name must not be empty", ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
            return;
        }

        /**
         * Asking for entrypoint
         */
        $entrypoint = Console::readLine("Entrypoint in class (camelCase):");
        if(trim($entrypoint) == '')  {
            Console::writeLine(ConsoleColors::getColoredString("FATAL: Webcomponent class name must not be empty", ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
            return;
        }

        /**
         * Asking for arguments
         */
        $continue = true;
        $argIndex = 1;
        $arguments = [];
        while($continue) {
            $arg = Console::readLine("Argument $argIndex:");
            if(trim($arg) == '')  {
                Console::writeLine(ConsoleColors::getColoredString("Ending Webcomponent arguments list", ConsoleColors::LIGHT_BLUE));
                $continue = false;
            }
            array_push($arguments, $arg);
            $argIndex++;
        }

        $destDir = SRC_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . $className;

        Utils::safeMkDir($destDir);
        $destDir = realpath($destDir);

        if (!file_exists($destDir)) {
            return;
        }

        $struct = new ManifestStructure([
            'tag' => $tagName, 
            'class' => $className,
            'entrypoint' => $entrypoint,
            'arguments' => $arguments,
        ]);
        $json = json_encode($struct->toArray(), JSON_PRETTY_PRINT);
        
        $destDir .= DIRECTORY_SEPARATOR;

        Utils::safeWrite($destDir . DIRECTORY_SEPARATOR . 'manifest.json', $json);

        $srcDir = EPHECT_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

        $classText = Utils::safeRead($srcDir . 'Base.class.mjs');
        $classText = str_replace('Base', $className, $classText);
        Utils::safeWrite($destDir . "$className.class.mjs", $classText);

        $componentText = Utils::safeRead($srcDir . 'Base.component.mjs');
        $componentText = str_replace('Base', $className, $componentText);
        Utils::safeWrite($destDir . "$className.component.mjs", $componentText);

        copy($srcDir . 'Base.tpl', $destDir . "$className.tpl");


    }
}
