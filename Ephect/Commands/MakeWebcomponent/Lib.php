<?php

namespace Ephect\Commands\CreateWebcomponent;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\IO\Utils;

class Lib
{

    public function createWebcomponentBase(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Webcomponents' . DIRECTORY_SEPARATOR . 'Templates';

        Console::writeLine(ConsoleColors::getColoredString("Creating a new webcomponent.", ConsoleColors::LIGHT_BLUE));
        Console::writeLine(ConsoleColors::getColoredString("Please, answer the following questions.", ConsoleColors::BLUE));
        Console::writeLine(ConsoleColors::getColoredString("Leave the answer blank to abort the process.", ConsoleColors::BROWN));

        /**
         * Asking the tag name
         */
        $tagName = Console::readLine("Tag name (kebab case style):");
        $tagName =  strtolower($tagName);
        if(trim($tagName) == '')  {
            Console::writeLine(ConsoleColors::getColoredString("FATAL: Webcomponent tag name must not be empty", ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
            return;
        }
        
        /**
         * Asking for the class name
         */
        $className = Console::readLine("Class name (to be imported):");
        if(trim($className) == '')  {
            Console::writeLine(ConsoleColors::getColoredString("FATAL: Webcomponent class name must not be empty", ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
            return;
        }

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

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }


        $tree = Utils::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}
