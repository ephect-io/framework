<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Core\AbstractApplication;
use Ephect\Framework\Element;
use Ephect\Framework\ElementUtils;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Framework\Utils\File;

class ApplicationCommands extends Element implements CommandCollectionInterface
{
    private array $_commands = [];

    public function __construct(private readonly AbstractApplication $_application)
    {
        parent::__construct($this->_application);

        $this->collectCommands();
    }

    private function collectCommands(): void
    {
        $usage = [];
        $commandFiles = File::walkTreeFiltered(COMMANDS_ROOT, ['php']);

        $allFiles = [
            (object)["root" => COMMANDS_ROOT, "files" => $commandFiles],
        ];


        if (file_exists(CUSTOM_COMMANDS_ROOT)) {
            $customCommandFiles = File::walkTreeFiltered(CUSTOM_COMMANDS_ROOT, ['php']);
            $allFiles[] = (object)["root" => CUSTOM_COMMANDS_ROOT, "files" => $customCommandFiles];
        }


        foreach ($allFiles as $entry) {
            $root_dir = $entry->root;
            foreach ($entry->files as $filename) {
                [$namespace, $class] = ElementUtils::getClassDefinitionFromFile($root_dir . $filename);
                $fqClass = "$namespace\\$class";

                if ($class !== 'Main') {
                    continue;
                }

                include $root_dir . $filename;
                $object = new $fqClass($this->_application);

                $attr = Element::getClassAttributesData($object);
                $commandArgs = $attr[0]['args'];

                $verb = $commandArgs['verb'];
                $subject = $commandArgs['subject'] ?? '';
                $desc = $commandArgs['desc'];
                $isPhar = $commandArgs['isPhar'] ?? '';

                if ($isPhar) {
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
        }

        ksort($usage);

        StateRegistry::writeItem('commands', $usage);
    }

    public function commands(): array
    {
        return $this->_commands;
    }
}
