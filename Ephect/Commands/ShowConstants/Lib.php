<?php

namespace Ephect\Commands\Constants;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Throwable;

class Lib
{

    public function displayConstants(): array
    {
        try {
            $constants = [];
            $constants['APP_NAME'] = APP_NAME;
            $constants['APP_CWD'] = APP_CWD;
            $constants['SCRIPT_ROOT'] = SCRIPT_ROOT;
            $constants['SRC_ROOT'] = SRC_ROOT;
            $constants['SITE_ROOT'] = SITE_ROOT;
            $constants['IS_PHAR_APP'] = IS_PHAR_APP ? 'TRUE' : 'FALSE';
            $constants['EPHECT_ROOT'] = EPHECT_ROOT;

            // $constants['EPHECT_VENDOR_SRC'] = EPHECT_VENDOR_SRC;
            // $constants['EPHECT_VENDOR_LIB'] = EPHECT_VENDOR_LIB;
            // $constants['EPHECT_VENDOR_APPS'] = EPHECT_VENDOR_APPS;

            if (APP_NAME !== 'egg') {
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

            Console::writeLine('Application constants are :');
            foreach ($constants as $key => $value) {
                Console::writeLine(ConsoleColors::getColoredString($key, ConsoleColors::CYAN) . ' => ' . ConsoleColors::getColoredString($value, ConsoleColors::BLUE));
            }

            return $constants;
        } catch (Throwable $ex) {
            Console::error($ex);

            return [];
        }
    }
}

