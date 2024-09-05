<?php

namespace Ephect\WebApp\Builder\Copiers;

use Ephect\WebApp\Builder\Copiers\Strategy\CopiersFactory;

class TemplatesCopyMaker
{
    public function makeCopies(bool $asUnique = false)
    {
        $copier = CopiersFactory::createCopier($asUnique);

        TemplatesCopier::copy($copier, SRC_ROOT, true);
        TemplatesCopier::copy($copier, CUSTOM_PAGES_ROOT);
        TemplatesCopier::copy($copier, CUSTOM_COMPONENTS_ROOT);

        //TODO: copy plugins templates
        //TODO: copy modules templates
    }

}