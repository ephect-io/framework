<?php

namespace Ephect\Apps\Egg;

use Ephect\CLI\Application;
use Ephect\Components\Compiler;
use Ephect\Element;
use Ephect\IO\Utils;
use Phar;

class PharLib extends Element
{

    private EggLib $_egg;
    private Phar $_phar;
    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);

        $this->egg = new EggLib($parent);

    }

    public function requireMaster(): object
    {
        $result = [];

        $libRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;

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
            $tree = Utils::walkTree($ephectDir, ['php']);
        }

        $result = ['path' => $ephectDir, 'tree' => $tree];

        return (object) $result;
    }

    public function addFileToPhar($file, $name): void
    {
        Console::writeLine("Adding %s", $name);
        $this->_phar->addFile($file, $name);
    }

    public function makeMasterPhar(): void
    {
        $ephectTree = $this->requireMaster();
        $this->_makePhar($ephectTree);
    }

    public function makeVendorPhar(): void
    {
        $this->_makePhar();
    }

    private function _makePhar(): void
    {
        try {

            // if (IS_WEB_APP) {
            //     throw new \Exception('Still cannot make a phar of a web application!');
            // }
            ini_set('phar.readonly', 0);

            // the current directory must be src
            $pharName = $this->appName . ".phar";
            $buildRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;

            if (file_exists($buildRoot . $pharName)) {
                unlink($buildRoot . $pharName);
            }

            if (!file_exists($buildRoot)) {
                mkdir($buildRoot);
            }

            $this->_phar = new \Phar(
                $buildRoot . $pharName,
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME,
                $pharName
            );
            // start buffering. Mandatory to modify stub.
            $this->_phar->startBuffering();

            // Get the default stub. You can create your own if you have specific needs
            $defaultStub = $this->_phar->createDefaultStub("app.php");

            Console::writeLine('APP_DIR::' . $this->appDirectory);
            $this->addPharFiles();

            $ephectTree = $this->_egg->requireTree(FRAMEWORK_ROOT);

            // $this->addFileToPhar(FRAMEWORK_ROOT . 'ephect_library.php', "ephect_library.php");

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
            $this->_phar->setStub($stub);

            $this->_phar->stopBuffering();

            $buildRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;
            $execname = $buildRoot . $this->appName;
            if (PHP_OS == 'WINNT') {
                $execname .= '.bat';
            }

            rename($buildRoot . $this->appName . '.phar', $execname);
            chmod($execname, 0755);
        } catch (\Throwable $ex) {
            Console::writeException($ex);
        }
    }

    public function addPharFiles(): void
    {
        try {
            $tree = Utils::walkTreeFiltered($this->appDirectory, ['php']);

            if (isset($tree[$this->appDirectory . $this->scriptName])) {
                unset($tree[$this->appDirectory . $this->scriptName]);
                $this->addFileToPhar($this->appDirectory . $this->scriptName, $this->scriptName);
            }
            foreach ($tree as $filename) {
                $this->addFileToPhar($this->appDirectory . $filename, $filename);
            }
        } catch (\Throwable $ex) {
            Console::writeException($ex);
        }
    }

}
