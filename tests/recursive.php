<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Ephect\CLI\Application;
use Ephect\Components\Component;
use Ephect\Components\ComponentEntityInterface;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Tree\TreeInterface;

class Program extends Application
{
    public static function main($argv, $argc)
    {
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {
        $func = 'Fun\\App';
        CodeRegistry::uncache();
        ComponentRegistry::uncache();
        // $flatEntity = CodeRegistry::read($func);
        $file = ComponentRegistry::read($func);
        // $entity = ComponentEntity::buildFromArray($flatEntity);

        /**
         * NEVER DO THAT
         */
        // $ra = new RecursiveArrayIterator($arrayEntity);
        // // $ri = new RecursiveIteratorIterator($ra);
        // $ri = new RecursiveIteratorIterator($ra, RecursiveIteratorIterator::CHILD_FIRST);

        // $names = [];
        // foreach ($ri as $k => $v) {
        //     if ($k === 'name') {
        //         array_push($names, $v);
        //     }
        //     $json = json_encode([$k => $v], JSON_PRETTY_PRINT);
        //     print $json;
        //     print '';
        // }
        /**
         * END NEVER DO THAT
         */

        $comp = new Component();
        $comp->load($file);
        $comp->compose();

        // $ri = $comp;
        $names = [];

        // $tree = $comp->getIterator();
        // $this->a($tree, $names);

        $this->c($comp, $names);

        print_r($names);
        // Utils::safeWrite('doc2.json', $json);
    }

    function d(TreeInterface $comp, callable $callback)
    {

        foreach ($comp as $k => $v) {
            call_user_func($callback, $v);

            if ($v->hasChildren()) {
                $this->d($v, $callback);
            }
        }
    }

    function c(TreeInterface $comp, array &$names = [])
    {

        $this->d($comp, function (ComponentEntityInterface $tree) use (&$names) {
            array_push($names, $tree->getName());
        });
    }

    function b(TreeInterface $comp, array &$names = [])
    {

        foreach ($comp as $k => $v) {
            array_push($names, $v->getName());
            if ($v->hasChildren()) {
                $this->b($v, $names);
            }
        }
    }
}

Program::main($argv, $argc);
