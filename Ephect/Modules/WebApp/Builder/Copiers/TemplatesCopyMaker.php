<?php

namespace Ephect\Modules\WebApp\Builder\Copiers;

use Ephect\Modules\WebApp\Builder\Copiers\Strategy\CopiersFactory;

class TemplatesCopyMaker
{
    public function makeCopies(bool $asUnique = false)
    {
        $copier = CopiersFactory::createCopier($asUnique);

        TemplatesCopier::copy($copier, \Constants::SRC_ROOT, true);
        TemplatesCopier::copy($copier, \Constants::CUSTOM_PAGES_ROOT);
        TemplatesCopier::copy($copier, \Constants::CUSTOM_COMPONENTS_ROOT);
    }

}
