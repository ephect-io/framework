<?php

namespace Ephect\Apps\Egg;

use Ephect\Apps\Egg\EggLib;
use Ephect\CLI\Application;
use Ephect\Commands\CommandCollectionInterface;
use Ephect\Element;

class EggCommands extends Element implements CommandCollectionInterface
{

    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);
    }


    public function commands(): array
    {
        $egg  = new EggLib($this->parent);

        return [
            [
                'long' => 'create',
                'short' => '',
                'description' => 'Create the application tree.',
                'callback' => function () use ($egg) {
                    $egg->createTree();
                }
            ],
            [
                'long' => 'delete',
                'short' => '',
                'description' => 'Delete the application tree.',
                'callback' => function () use ($egg) {
                    $egg->deleteTree();
                }
            ],
            [
                'long' => 'compile',
                'short' => 'c',
                'description' => 'compile all components of the application so they are readable by PHP processor',
                'callback' => function () use ($egg) {
                    $egg->compile();
                }
            ],
            [
                'long' => 'sample',
                'short' => 's',
                'description' => 'create the sample application',
                'callback' => function () use ($egg) {
                    $egg->sample();
                }
            ],
        ];
    }
}
