<?php

namespace Ephect\Commands\MakeCommand;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\ConsoleOptions;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Commands\Builder;
use Exception;

class Lib extends AbstractCommandLib
{

    public function createCommandBase(): void
    {
        try {

            Console::writeLine(ConsoleColors::getColoredString("Creating a new command.", ConsoleColors::LIGHT_BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Please, answer the following questions.", ConsoleColors::BLUE));
            Console::writeLine(ConsoleColors::getColoredString("Leaving the answer blank aborts the process on mandatory questions.", ConsoleColors::BROWN));
            Console::writeLine(ConsoleColors::getColoredString("Mandatory questions are marked with *.", ConsoleColors::BROWN));

            $builder = new Builder;
            [$verb, $subject, $description, $methodName, $arguments] = $this->readLine();

            $commandName = $subject != "" ? $verb . ":" . $subject : ucfirst($verb);
            $commandDirectory = $subject != "" ? ucfirst($verb) . ucfirst($subject) : ucfirst($verb);

            $destDir = SRC_ROOT . 'Commands' . DIRECTORY_SEPARATOR . $commandDirectory . DIRECTORY_SEPARATOR;
            $srcDir = EPHECT_ROOT . 'Templates' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR;
            $builder->copyTemplates($verb, $subject, $description, $methodName, $arguments, $srcDir, $destDir);

            Console::writeLine(ConsoleColors::getColoredString("Command ", ConsoleColors::BLUE) . "%s" . ConsoleColors::getColoredString(" is available in:", ConsoleColors::BLUE), $commandName);
            Console::writeLine("%s", $destDir);
        } catch (Exception $ex) {
            Console::error($ex, ConsoleOptions::ErrorMessageOnly);
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
         * Asking for the verb
         */
        $verb = Console::readLine("* Verb (kebab-case, eg: make, create, clear, etc.): ");
        $verb = strtolower($verb);
        if (trim($verb) == '') {
            throw new Exception("Verb must not be empty");
        }

        /**
         * Asking for the subject
         */
        $subject = Console::readLine("Subject (kebab-case): ");
        $subject = strtolower($subject);

        /**
         * Asking for the description
         */
        $description = Console::readLine("* Description: ");
        if (trim($description) == '') {
            throw new Exception("* Description must not be empty");
        }

        /**
         * Asking for the method name
         */
        $methodName = Console::readLine("* Method name (camelCase): ");
        if (trim($methodName) == '') {
            throw new Exception("Method name must not be empty");
        }

        /**
         * Asking for arguments
         */
        $next = true;
        $argIndex = 1;
        $arguments = [];
        while ($next) {
            $arg = Console::readLine("Argument $argIndex: ");
            if (trim($arg) == '') {
                Console::writeLine(ConsoleColors::getColoredString("Ending command arguments list", ConsoleColors::LIGHT_BLUE));
                $next = false;
                continue;
            }
            $arguments[] = $arg;
            $argIndex++;
        }

        return [$verb, $subject, $description, $methodName, $arguments];
    }
}
