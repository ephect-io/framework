<?php

namespace Ephect\CLI;

use Ephect\Commands\CommandCollectionInterface;
use Ephect\Element;
use Ephect\ElementUtils;
use Ephect\IO\Utils;

class ApplicationCommands extends Element implements CommandCollectionInterface
{
    private array $_commands = [];

    public function __construct(private Application $_application)
    {
        $this->collectCommands();
    }

    public function commands(): array
    {
        return $this->_commands;
    }

    private function collectCommands(): void
    {
        $commandFiles = Utils::walkTreeFiltered(COMMANDS_ROOT, ['php']);

        foreach ($commandFiles as $filename) {

            [$namespace, $class] = ElementUtils::getClassDefinitionFromFile(COMMANDS_ROOT . $filename);
            $fqClass = "$namespace\\$class";

            include COMMANDS_ROOT . $filename;
            $object = new $fqClass($this->_application);

            $attr = Element::getAttributesData($object);
            $commandArgs = $attr[0]['args'];

            $commandArgs['callback'] = $object;

            $this->_commands[] = $commandArgs;
        }
    }
}
