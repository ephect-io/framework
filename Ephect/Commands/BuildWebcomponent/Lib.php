<?php

namespace Ephect\Commands\BuildWebcomponent;

class Lib
{

    public function build(): void
    {
        CodeRegistry::uncache();

        $templateList = IOUtils::walkTreeFiltered(SITE_ROOT . CONFIG_WEBCOMPONENTS, ['phtml']);
        foreach ($templateList as $key => $filename) {
            $uid = ComponentRegistry::read($filename);
            // $comp = new Component($uid, $motherUID);
            // $comp->load($filename);
        }



    }
}

