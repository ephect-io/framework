<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Core\AbstractApplication;
use Ephect\Framework\Element;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Registry\StateRegistry;

class ApplicationCommands extends Element implements CommandCollectionInterface
{
    private array $_commands = [];

    public function __construct(private AbstractApplication $_application)
    {
        $this->collectCommands();
    }

    public function commands(): array
    {
        return $this->_commands;
    }

    private function collectCommands(): void
    {
        $usage = [];
        $commandFiles = Utils::walkTreeFiltered(COMMANDS_ROOT, ['php']);

        foreach ($commandFiles as $filename) {

            [$namespace, $class] = ElementUtils::getClassDefinitionFromFile(COMMANDS_ROOT . $filename);
            $fqClass = "$namespace\\$class";

            include COMMANDS_ROOT . $filename;
            $object = new $fqClass($this->_application);

            $attr = Element::getAttributesData($object);
            $commandArgs = $attr[0]['args'];

            $verb = $commandArgs['verb'];
            $subject = isset($commandArgs['subject']) ? $commandArgs['subject'] : '';
            $desc = $commandArgs['desc'];
            $isPhar = isset($commandArgs['isPhar']) ? $commandArgs['isPhar'] : '';

            if($isPhar) {
                continue;
            }

            if ($subject !== '') {
                $usage[$verb . $subject] = "\t$verb:$subject => $desc" . PHP_EOL;
            } else {
                $usage[$verb] = "\t$verb => $desc" . PHP_EOL;
            }
            $commandArgs['callback'] = $object;

            $this->_commands[] = $commandArgs;
        }

        ksort($usage);

        StateRegistry::write('commands', $usage);
    }
}
