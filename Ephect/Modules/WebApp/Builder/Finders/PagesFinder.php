<?php

namespace Ephect\Modules\WebApp\Builder\Finders;

use Ephect\Framework\Utils\File;

class PagesFinder implements FinderInterface
{
    public function find(): array
    {
        $result = [];

        $pagesList = File::walkTreeFiltered(\Constants::COPY_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            $result[] = $pageFile;
        }

        return $result;
    }
}
