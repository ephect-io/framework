<?php

namespace Ephect\Modules\Forms;

use Ephect\Framework\Modules\ModuleBootstrapInterface;
use Ephect\JavaScripts\Builder\AjilBuilder;

class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        if (!file_exists(\Constants::DOCUMENT_ROOT . 'ajil.js')) {
            AjilBuilder::build();
        }
    }
}
