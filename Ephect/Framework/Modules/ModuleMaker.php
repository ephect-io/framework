<?php

namespace Ephect\Framework\Modules;

class ModuleMaker
{
    public static function makeTemplate(string $filename, array $params): string
    {
        $keys = array_keys($params);
        $template = file_get_contents(MODULE_SRC_DIR . 'Templates'  . DIRECTORY_SEPARATOR . $filename);

        if(count($keys) == 0)  {
            return $template;
        }

        foreach($keys as $key) {
            $template = str_replace('{{' . $key . '}}', $params[$key], $template);
        }

        return $template;

    }
}