<?php

namespace Ephect\WebApp\Web;

use Ephect\Forms\Components\Component;
use Ephect\Forms\Registry\CacheRegistry;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Forms\Registry\PluginRegistry;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Core\AbstractApplication;
use Ephect\Framework\Registry\HooksRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\WebApp\Builder\Builder;

class Application extends AbstractApplication
{
    private string $html = '';

    public static function create(...$params): self
    {
        self::$instance = new Application();
        self::$instance->run(...$params);

        return self::$instance;
    }

    public function run(...$params): int
    {
        $this->loadInFile();
        StateRegistry::load();
        if (!ComponentRegistry::load()) {
            $compiler = new Builder;
            $compiler->describeComponents();
            $compiler->prepareRoutedComponents();
        }

        CacheRegistry::load();
        PluginRegistry::load();
        HooksRegistry::register(APP_ROOT);

        $this->execute();

        return 0;
    }

    protected function execute(): int
    {
        $app = new Component('App');

//        ob_start();
        $app->render();
//        $this->html = ob_get_clean();

        return 0;
    }

    public function getHtml(): string
    {
        return $this->html;
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

        StateRegistry::writeItem('console', 'buffer', $constants);

        Console::Log('Application constants are :');
        foreach ($constants as $key => $value) {
            Console::Log($key . ' => ' . $value);
        }

        return $constants;
    }


}
