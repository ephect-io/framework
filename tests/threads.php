<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Closure;
use Ephect\CLI\Application;
use Ephect\IO\Utils;
use Error;
use Exception;
use parallel\Runtime;
use parallel\Channel;

class Program extends Application
{
    protected $routes = [];
    protected $callback = null;

    public static function main($argv, $argc)
    {
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {
        $this->add('Hello');
        $this->add('World');

        $this->callback(function (string $word, Channel $ch) {

            // the only way to share data is between channels
            ob_start();
            echo strtoupper($word);
            $html = ob_get_clean();

            $ch->send($html);
        });

        $this->test();
    }

    public function add(string $route): void
    {
        $this->routes[] = $route;
    }

    public function callback(Closure $callback): void
    {
        $this->callback = $callback;
    }

    public function test(): void
    {
        // this function will be the threads
        $thread_function = $this->callback;


        try {
            $ch1 = new Channel();

            $result = [];
            foreach ($this->routes as $route) {
                $r1 = new Runtime();
                $args = array();

                $args[0] = $route;
                $args[1] = $ch1;
                $r1->run($thread_function, $args);

                $result[$route] = $ch1->recv();
            }

            // close channel
            $ch1->close();

            echo "\nData received by the channel: " . implode(' and ', $result);
        } catch (Error $err) {
            echo "\nError:", $err->getMessage();
        } catch (Exception $e) {
            echo "\nException:", $e->getMessage();
        }
    }
}

Program::main($argv, $argc);
