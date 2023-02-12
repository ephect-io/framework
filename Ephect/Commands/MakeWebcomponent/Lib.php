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

            $destDir = SRC_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . $className;

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

            $srcDir = EPHECT_ROOT . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

            $classText = Utils::safeRead($srcDir . 'Base.class.mjs');
            $classText = str_replace('Base', $className, $classText);
            Utils::safeWrite($destDir . "$className.class.mjs", $classText);

            $componentText = Utils::safeRead($srcDir . 'Base.component.mjs');
            $componentText = str_replace('Base', $className, $componentText);
            Utils::safeWrite($destDir . "$className.component.mjs", $componentText);

            copy($srcDir . 'Base.tpl', $destDir . "$className.tpl");
            
        } catch (Exception $ex) {
            Console::writeException($ex);
        }
    }
}
