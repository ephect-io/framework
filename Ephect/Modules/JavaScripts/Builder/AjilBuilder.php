<?php

namespace Ephect\JavaScripts\Builder;

use Ephect\Framework\Utils\File;
use Ephect\Modules\JavaScripts\Lib\Common;

class AjilBuilder
{
    public static function build(): void
    {

        $common = new Common();
        $srcdir = $common->getModuleSrcDir() . 'Assets' . DIRECTORY_SEPARATOR . 'Ajil' . DIRECTORY_SEPARATOR;

        $js_filename = \Constants::DOCUMENT_ROOT . 'ajil.js';

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

        File::safeWrite($js_filename, $js_content);
    }
}
