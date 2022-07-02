<?php

namespace Ephect\Framework\JavaScripts;

use Ephect\Framework\IO\Utils;

class AjilBuilder
{

    public static function build(): void
    {

        // $srcdir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bower_components' . DIRECTORY_SEPARATOR . 'phinkjs' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR;
        // $srcdir = SRC_ROOT . 'web' . DIRECTORY_SEPARATOR . 'bower_components' . DIRECTORY_SEPARATOR . 'phinkjs' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR;
        $srcdir = AJIL_ROOT;

        $js_filename = DOCUMENT_ROOT . 'ajil.js';

        $filenames = [
            'core/main.js',
            'core/registry.js',
            'core/object.js',
            'core/url.js',
            'rest/rest.js',
            'web/web_object.js',
            'web/web_application.js',
            'mvc/view.js',
            'mvc/controller.js',
            'utils/utils.js',
            'utils/backend.js',
            'utils/commands.js',
            'web/ui/plugin.js',
            'web/ui/plugin/accordion.js',
            'web/ui/plugin/list.js',
            'web/ui/plugin/table.js',
            'bootstrap.js',
        ];

        $js_content = '';

        foreach ($filenames as $filename) {
            $js_content .= file_get_contents($srcdir . $filename);
        }

        Utils::safeWrite($js_filename, $js_content);
    }
}
