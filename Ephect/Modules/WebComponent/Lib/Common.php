<?php

namespace Ephect\Modules\WebComponent;

use Ephect\Framework\Modules\Utils;

class Common extends Utils
{

    public function __construct()
    {
        parent::__construct(__DIR__);
    }

    public function getCustomWebComponentRoot(): string
    {
        $moduleTemplatesDir = $this->getModuleManifest()->getTemplates();
        $customConfig = file_exists(CONFIG_DIR . 'webcomponents') ?
            trim(file_get_contents(CONFIG_DIR . 'webcomponents')) : $moduleTemplatesDir;
        return SRC_ROOT . $customConfig . DIRECTORY_SEPARATOR;
    }

}