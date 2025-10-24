<?php

namespace Ephect\Commands\ShowConstants;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Throwable;

class Lib extends AbstractCommandLib
{
    public function displayConstants(): array
    {
        try {
            $constants = [];
            $constants['APP_NAME'] = \Constants::APP_NAME;
            $constants['APP_CWD'] = \Constants::APP_CWD;
            $constants['SCRIPT_ROOT'] = \Constants::SITE_ROOT;
            $constants['SRC_ROOT'] = \Constants::SRC_ROOT;
            $constants['SITE_ROOT'] = \Constants::SITE_ROOT;
            $constants['IS_PHAR_APP'] = \Constants::IS_PHAR_APP ? 'true' : 'false';
            $constants['EPHECT_ROOT'] = \Constants::EPHECT_ROOT;

            // $constants['EPHECT_VENDOR_SRC'] = \Constants::EPHECT_VENDOR_SRC;
            // $constants['EPHECT_VENDOR_LIB'] = \Constants::EPHECT_VENDOR_LIB;
            // $constants['EPHECT_VENDOR_APPS'] = \Constants::EPHECT_VENDOR_APPS;

            if (\Constants::APP_NAME !== 'use') {
                $constants['APP_ROOT'] = \Constants::APP_ROOT;
                $constants['APP_SCRIPTS'] = \Constants::APP_SCRIPTS;
                $constants['APP_BUSINESS'] = \Constants::APP_BUSINESS;
                $constants['MODEL_ROOT'] = \Constants::MODEL_ROOT;
                $constants['VIEW_ROOT'] = \Constants::VIEW_ROOT;
                $constants['CONTROLLER_ROOT'] = \Constants::CONTROLLER_ROOT;
                $constants['REST_ROOT'] = \Constants::REST_ROOT;
                $constants['APP_DATA'] = \Constants::APP_DATA;
                $constants['CACHE_DIR'] = \Constants::CACHE_DIR;
            }
            $constants['LOG_PATH'] = \Constants::LOG_PATH;
            $constants['DEBUG_LOG'] = \Constants::DEBUG_LOG;
            $constants['ERROR_LOG'] = \Constants::ERROR_LOG;

            Console::writeLine('Application constants are :');
            foreach ($constants as $key => $value) {
                Console::writeLine(
                    ConsoleColors::getColoredString($key, ConsoleColors::CYAN)
                    . ' => '
                    . ConsoleColors::getColoredString($value, ConsoleColors::BLUE)
                );
            }

            return $constants;
        } catch (Throwable $ex) {
            Console::error($ex);

            return [];
        }
    }
}
