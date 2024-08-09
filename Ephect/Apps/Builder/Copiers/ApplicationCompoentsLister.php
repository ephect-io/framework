<?php

namespace Ephect\Apps\Builder\Copiers;

use Ephect\Framework\Utils\File;

class ApplicationCompoentsLister implements FilesListerInterface
{

    public function list(string $path): array
    {
        $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
    }
}