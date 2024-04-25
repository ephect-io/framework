<?php

namespace Ephect\Apps\Egg;

use Ephect\Commands\CommonLib;
use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Element;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Zip;
use Ephect\Framework\Web\Curl;
use Exception;
use FilesystemIterator;
use Phar;
use Throwable;

class PharLib extends Element
{

    private EggLib $egg;
    private Phar $phar;

    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);

        $this->egg = new CommonLib($parent);

    }

    public function makeMasterPhar(): void
    {
        $ephectTree = $this->requireMaster();
        $this->_makePhar($ephectTree);
    }

    /**
     * @throws Exception
     */
    public function requireMaster(): object
    {
        $result = [];

        $libRoot = APP_CWD . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;

        if (!file_exists($libRoot)) {
            mkdir($libRoot);
        }

        $master = $libRoot . 'master';
        $filename = $master . '.zip';
        $ephectDir = $master . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR;

        $tree = [];

        if (!file_exists($filename)) {
            Console::writeLine('Downloading ephect github master');
            $curl = new Curl();
            $result = $curl->request('https://codeload.github.com/CodePhoenixOrg/ephect/zip/master');
            file_put_contents($filename, $result->content);
        }

        if (file_exists($filename)) {
            Console::writeLine('Inflating ephect master archive');
            $zip = new Zip();
            $zip->inflate($filename);
        }

        if (file_exists($master)) {
            $php = ['php'];
            $tree = File::walkTree($ephectDir, $php);
        }

        $result = ['path' => $ephectDir, 'tree' => $tree];

        return (object)$result;
    }

    private function _makePhar(): void
    {
        try {

            // if (IS_WEB_APP) {
            //     throw new \Exception('Still cannot make a phar of a web application!');
            // }
            ini_set('phar.readonly', 0);

            // the current directory must be src
            $pharName = APP_NAME . ".phar";
            $buildRoot = APP_CWD . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;

            if (file_exists($buildRoot . $pharName)) {
                unlink($buildRoot . $pharName);
            }

            if (!file_exists($buildRoot)) {
                mkdir($buildRoot);
            }

            $this->phar = new Phar(
                $buildRoot . $pharName,
                FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
                $pharName
            );
            // start buffering. Mandatory to modify stub.
            $this->phar->startBuffering();

            // Get the default stub. You can create your own if you have specific needs
            $defaultStub = $this->phar->createDefaultStub("app.php");

            Console::writeLine('APP_DIR::' . APP_CWD);
            $this->addPharFiles();

            $ephectTree = $this->egg->requireTree(EPHECT_ROOT);

            // $this->addFileToPhar(EPHECT_ROOT . 'ephect_library.php', "ephect_library.php");

            foreach ($ephectTree->tree as $file) {
                $filepath = $ephectTree->path . $file;
                $filepath = realpath($filepath);
                $filename = str_replace(DIRECTORY_SEPARATOR, '_', $file);

                $this->addFileToPhar($filepath, $filename);
            }

            // $hooksTree = $this->_requireTree(HOOKS_ROOT);

            // foreach ($hooksTree->tree as $file) {
            //     $filepath = $hooksTree->path . $file;
            //     $filepath = realpath($filepath);
            //     $filename = str_replace(DIRECTORY_SEPARATOR, '_', $file);

            //     $this->addFileToPhar($filepath, $filename);
            // }

            // $pluginsTree = $this->_requireTree(PLUGINS_ROOT);

            // foreach ($pluginsTree->tree as $file) {
            //     $filepath = $pluginsTree->path . $file;
            //     $filepath = realpath($filepath);
            //     $filename = str_replace(DIRECTORY_SEPARATOR, '_', $file);

            //     $this->addFileToPhar($filepath, $filename);
            // }

            // Create a custom stub to add the shebang
            $execHeader = "#!/usr/bin/env php \n";
            if (PHP_OS == 'WINNT') {
                $execHeader = "@echo off\r\nphp.exe\r\n";
            }

            $stub = $execHeader . $defaultStub;
            // Add the stub
            $this->phar->setStub($stub);

            $this->phar->stopBuffering();

            $buildRoot = APP_CWD . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;
            $execname = $buildRoot . APP_NAME;
            if (PHP_OS == 'WINNT') {
                $execname .= '.bat';
            }

            rename($buildRoot . APP_NAME . '.phar', $execname);
            chmod($execname, 0755);
        } catch (Throwable $ex) {
            Console::error($ex);
        }
    }

    public function addPharFiles(): void
    {
        try {
            $tree = File::walkTreeFiltered(APP_CWD, ['php']);

            if (isset($tree[APP_CWD . APP_NAME])) {
                unset($tree[APP_CWD . APP_NAME]);
                $this->addFileToPhar(APP_CWD . APP_NAME, SCRIPT_ROOT);
            }
            foreach ($tree as $filename) {
                $this->addFileToPhar(APP_CWD . $filename, $filename);
            }
        } catch (Throwable $ex) {
            Console::error($ex);
        }
    }

    public function addFileToPhar($file, $name): void
    {
        Console::writeLine("Adding %s", $name);
        $this->phar->addFile($file, $name);
    }

    public function makeVendorPhar(): void
    {
        $this->_makePhar();
    }

}
