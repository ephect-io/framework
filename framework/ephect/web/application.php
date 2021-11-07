<?php

namespace Ephect\Web;

use Ephect\CLI\Application as CLIApplication;
use Ephect\Components\Compiler;
use Ephect\Components\Component;
use Ephect\Core\AbstractApplication;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;
use Ephect\Registry\Registry;

class Application extends AbstractApplication
{

    public static function create(...$params): void
    {
        session_start();
        self::$instance = new Application();
        self::$instance->run(...$params);
    }

    public function run(...$params): void
    {
        $this->loadInFile();

        if (!ComponentRegistry::uncache()) {
            $compiler = new Compiler;
            $compiler->perform();
            $compiler->postPerform();
        }
        if (!CacheRegistry::uncache()) {
            PluginRegistry::uncache();
        }

        $app = new Component('App');
        $app->render();
    }

    public function displayConstants(): array
    {
        $constants = [];
        $constants['REWRITE_BASE'] = REWRITE_BASE;
        $constants['DOCUMENT_ROOT'] = DOCUMENT_ROOT;
        $constants['HTTP_PROTOCOL'] = HTTP_PROTOCOL;
        $constants['SRC_ROOT'] = SRC_ROOT;
        $constants['EPHECT_ROOT'] = EPHECT_ROOT;
        $constants['APP_NAME'] = APP_NAME;
        $constants['APP_ROOT'] = APP_ROOT;
        $constants['CONTROLLER_ROOT'] = CONTROLLER_ROOT;
        $constants['MODEL_ROOT'] = MODEL_ROOT;
        $constants['REST_ROOT'] = REST_ROOT;
        $constants['VIEW_ROOT'] = VIEW_ROOT;
        $constants['BUSINESS_ROOT'] = BUSINESS_ROOT;
        $constants['REL_RUNTIME_DIR'] = REL_RUNTIME_DIR;
        $constants['RUNTIME_DIR'] = RUNTIME_DIR;
        $constants['REL_RUNTIME_JS_DIR'] = REL_RUNTIME_JS_DIR;
        $constants['RUNTIME_JS_DIR'] = RUNTIME_JS_DIR;
        $constants['CACHE_DIR'] = CACHE_DIR;
        $constants['LOG_PATH'] = LOG_PATH;
        $constants['DEBUG_LOG'] = DEBUG_LOG;
        $constants['ERROR_LOG'] = ERROR_LOG;
        $constants['APP_DATA'] = APP_DATA;
        $constants['APP_BUSINESS'] = APP_BUSINESS;
        $constants['STARTER_FILE'] = STARTER_FILE;
        $constants['HTTP_USER_AGENT'] = HTTP_USER_AGENT;
        $constants['HTTP_HOST'] = HTTP_HOST;
        $constants['HTTP_ORIGIN'] = HTTP_ORIGIN;
        $constants['HTTP_ACCEPT'] = HTTP_ACCEPT;
        $constants['HTTP_PORT'] = HTTP_PORT;
        $constants['REQUEST_URI'] = REQUEST_URI;
        $constants['REQUEST_METHOD'] = REQUEST_METHOD;
        $constants['QUERY_STRING'] = QUERY_STRING;
        $constants['SERVER_NAME'] = SERVER_NAME;
        $constants['SERVER_HOST'] = SERVER_HOST;
        $constants['SERVER_ROOT'] = SERVER_ROOT;
        $constants['BASE_URI'] = BASE_URI;
        $constants['FULL_URI'] = FULL_URI;
        $constants['FULL_SSL_URI'] = FULL_SSL_URI;
        $constants['ROOT_NAMESPACE'] = ROOT_NAMESPACE;
        $constants['ROOT_PATH'] = ROOT_PATH;

        Registry::write('console', 'buffer', $constants);

        CLIApplication::writeLine('Application constants are :');
        foreach ($constants as $key => $value) {
            CLIApplication::writeLine($key . ' => ' . $value);
        }

        return $constants;
    }

  
}
