<?php

namespace Ephect\Commands\MakeWebComponent;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\WebComponents\Builder;
use Ephect\Framework\WebComponents\ManifestStructure;
use Exception;

class Lib extends AbstractCommandLib
{

    public function createWebcomponentBase(): void
    {
        try {

            Console::writeLine(ConsoleColors::getColoredString("Creating a new webComponent.", ConsoleColors::LIGHT_BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Please, answer the following questions.", ConsoleColors::BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Leave the answer blank to pass to the next question or to abort the process.", ConsoleColors::BROWN));

            $builder = new Builder;
            [$tagName, $className, $entrypoint, $arguments] = $this->readLine();

            $destDir = SRC_ROOT . DIRECTORY_SEPARATOR . 'WebComponents' . DIRECTORY_SEPARATOR . $className . DIRECTORY_SEPARATOR;

            $builder->saveManifest($tagName, $className, $entrypoint, $arguments, $destDir);

            $srcDir = EPHECT_ROOT . DIRECTORY_SEPARATOR . 'WebComponents' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;

            $builder->copyTemplates($tagName, $className, $entrypoint, $arguments, $srcDir, $destDir);

            Console::writeLine(ConsoleColors::getColoredString("WebComponent ", ConsoleColors::BLUE) . "%s" .  ConsoleColors::getColoredString(" is available in:", ConsoleColors::BLUE), $className);
            Console::writeLine("%s", $destDir);
        } catch (Exception $ex) {
            Console::error($ex);
        }
    }

    /**
     * Ask some questions to get the properties of the webComponent
     *
     * @return array
     * @throws Exception
     */
    function readLine(): array
    {
        /**
         * Asking the tag name
         */
        $tagName = Console::readLine("Tag name (kebab-case):");
        $tagName =  strtolower($tagName);
        if (trim($tagName) == '') {
            throw new Exception("WebComponent tag name must not be empty");
        }

        Console::writeLine(ConsoleColors::getColoredString("The code of the webComponent will split into one JS module and one HTML template.", ConsoleColors::BLUE));

        /**
         * Asking for the class name
         */
        $className = Console::readLine("Module class name (PascalCase):");
        if (trim($className) == '') {
            throw new Exception("WebComponent class name must not be empty");
        }

        /**
         * Asking for entrypoint
         */
        $entrypoint = Console::readLine("Entrypoint in class (camelCase):");
        if (trim($entrypoint) == '') {
            throw new Exception("WebComponent entrypoint must not be empty");
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
                Console::writeLine(ConsoleColors::getColoredString("Ending WebComponent arguments list", ConsoleColors::LIGHT_BLUE));
                $next = false;
                continue;
            }
            $arguments[] = $arg;
            $argIndex++;
        }

        return [$tagName, $className, $entrypoint, $arguments];
    }

   

}
