<?php

namespace Ephect\CLI;

use Ephect\Commands\CommandCollectionInterface;
use Ephect\Element;
use Ephect\ElementUtils;
use Ephect\IO\Utils;

class ApplicationCommands extends Element implements CommandCollectionInterface
{
    private $_commands = [];

    public function __construct(private Application $_application)
    {
        $commandFiles = Utils::walkTreeFiltered(COMMANDS_ROOT, ['php']);

        foreach ($commandFiles as $filename) {

            [$namespace, $class] = ElementUtils::getClassDefinitionFromFile(COMMANDS_ROOT . $filename);
            $fqClass = "$namespace\\$class";

            include COMMANDS_ROOT . $filename;
            $object = new $fqClass($_application);

            $attr = Element::getAttributesData($object);
            $commandArgs = $attr[0]['args'];

            $commandArgs['callback'] = $object;

            $this->_commands[] = $commandArgs;
        }
    }


    public function commands(): array
    {
        return $this->_commands;
    }

    public function commandsEx(): array
    {
        return [

            [
                'long' => 'help',
                'short' => 'h',
                'description' => 'Display this help',
                'callback' => function (callable $callback = null) {
                    $this->_application->help();
                    $data = $this->_usage;
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'ini',
                'short' => '',
                'description' => 'Display the ini file if exists',
                'callback' => function (callable $callback = null) {
                    $this->_application->loadInFile();
                    $data = Registry::item('ini');
                    $this->_application->writeLine($data);
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'os',
                'short' => '',
                'description' => 'Display the running operating system name.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->getOS();
                    $this->_application->writeLine($data);
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'name',
                'short' => '',
                'description' => 'Display the running application name.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->getName();
                    $this->_application->writeLine($data);
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'title',
                'short' => '',
                'description' => 'Display the running application title.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->getTitle();
                    $this->_application->writeLine($data);
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'constants',
                'short' => '',
                'description' => 'Display the application constants.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->displayConstants();
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'debug',
                'short' => '',
                'description' => 'Display the debug log.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->getDebugLog();
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'info-modules',
                'short' => '',
                'description' => 'Display the module section of phpinfo() output.',
                'callback' => function (callable $callback = null) {
                    $info = new PhpInfo();
                    $data = $info->getModulesSection(true);
                    // ob_start();
                    // phpinfo(INFO_MODULES);
                    // $data = ob_get_clean();
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'connections',
                'short' => '',
                'description' => 'Display the data connections registered.',
                'callback' => function (callable $callback = null) {
                    $data = Registry::item('connections');
                    $this->_application->writeLine($data);

                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'error',
                'short' => '',
                'description' => 'Display the php error log.',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->getPhpErrorLog();
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'rlog',
                'short' => '',
                'description' => 'All log files cleared',
                'callback' => function (callable $callback = null) {
                    $data = $this->_application->clearLogs();
                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],
            [
                'long' => 'running',
                'short' => '',
                'description' => 'Show Phar::running() output',
                'callback' => function () {
                    $this->_application->writeLine(\Phar::running());
                }
            ],

            [
                'long' => 'source-path',
                'short' => '',
                'description' => 'Display the running application source directory.',
                'callback' => function () {
                    $this->_application->writeLine($this->_application->getDirectory());
                }
            ],

            [
                'long' => 'script-path',
                'short' => '',
                'description' => 'Display the running application root.',
                'callback' => function () {
                    $this->_application->writeLine(SCRIPT_ROOT);
                }
            ],

            [
                'long' => 'display-tree',
                'short' => '',
                'description' => 'Display the tree of the current application.',
                'callback' => function () {
                    $this->_application->displayTree($this->_application->appDirectory);
                }
            ],

            [
                'long' => 'display-ephect-tree',
                'short' => '',
                'description' => 'Display the tree of the ephect command.',
                'callback' => function () {
                    $this->_application->displayephectTree();
                }
            ],

            [
                'long' => 'display-master-tree',
                'short' => '',
                'description' => 'Display the tree of the master branch of ephect command previously downloaded.',
                'callback' => function () {
                    try {
                        $this->_application->displayTree('master' . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect');
                    } catch (\Throwable $ex) {
                        $this->_application->writeException($ex);
                    }
                }
            ],

            [
                'long' => 'show-arguments',
                'short' => '',
                'description' => 'Show the application arguments.',
                'callback' => function (callable $callback = null) {
                    $data = ['argv' => $this->_application->getArgv(), 'argc' => $this->_application->getArgc()];
                    $this->_application->writeLine($data);

                    if ($callback !== null) {
                        \call_user_func($callback, $data);
                    }
                }
            ],

            [
                'long' => 'display-history',
                'short' => '',
                'description' => 'Display commands history.',
                'callback' => function () {
                    try {
                        $this->_application->writeLine(readline_list_history());
                    } catch (\Throwable $ex) {
                        $this->_application->writeException($ex);
                    }
                }
            ],


        ];
    }
}
