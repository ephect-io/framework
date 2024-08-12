<?php

namespace Ephect\Apps\Builder\Copiers;

use Ephect\Apps\Builder\Copiers\Strategy\CopiersFactory;
use Ephect\Apps\Builder\Copiers\Strategy\CopierStrategyInterface;
use Ephect\Framework\Utils\File;

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