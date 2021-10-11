<?php

namespace Ephect\CLI;

use Ephect\Core\AbstractApplication;

class Application extends AbstractApplication
{

    protected function displayConstants(): array 
    { 
        try {
            $constants = [];
            $constants['APP_NAME'] = APP_NAME;
            $constants['SCRIPT_ROOT'] = SCRIPT_ROOT;
            $constants['SITE_ROOT'] = SITE_ROOT;
            $constants['APP_IS_PHAR'] = APP_IS_PHAR ? 'TRUE' : 'FALSE';
            $constants['PHINK_ROOT'] = PHINK_ROOT;

            // $constants['PHINK_VENDOR_SRC'] = PHINK_VENDOR_SRC;
            // $constants['PHINK_VENDOR_LIB'] = PHINK_VENDOR_LIB;
            // $constants['PHINK_VENDOR_APPS'] = PHINK_VENDOR_APPS;

            if (APP_NAME !== 'egg') {
                $constants['SRC_ROOT'] = SRC_ROOT;
                $constants['APP_ROOT'] = APP_ROOT;
                $constants['APP_SCRIPTS'] = APP_SCRIPTS;
                $constants['APP_BUSINESS'] = APP_BUSINESS;
                $constants['MODEL_ROOT'] = MODEL_ROOT;
                $constants['VIEW_ROOT'] = VIEW_ROOT;
                $constants['CONTROLLER_ROOT'] = CONTROLLER_ROOT;
                $constants['REST_ROOT'] = REST_ROOT;
                $constants['APP_DATA'] = APP_DATA;
                $constants['CACHE_DIR'] = CACHE_DIR;
            }
            $constants['LOG_PATH'] = LOG_PATH;
            $constants['DEBUG_LOG'] = DEBUG_LOG;
            $constants['ERROR_LOG'] = ERROR_LOG;

            $this->writeLine('Application constants are :');
            foreach ($constants as $key => $value) {
                $this->writeLine("\033[0m\033[0;36m" . $key . "\033[0m\033[0;33m" . ' => ' . "\033[0m\033[0;34m" . $value . "\033[0m\033[0m");
            }

            return $constants;
        } catch (\Throwable $ex) {
            $this->writeException($ex);
            
            return [];
        }
    }

    public static function create(...$params): void
    {
        self::$instance = new Application();
        self::$instance->run($params);
    }

    public function run(?array ...$params) : void
    {
    }
}
