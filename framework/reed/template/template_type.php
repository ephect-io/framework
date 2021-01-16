<?php
namespace Reed\Template;

use Reed\Core\Enumerator;

class ETemplateType extends Enumerator
{
    public const NON_PHINK_TEMPLATE = 0;
    public const PHINK_SERVER_TEMPLATE = 1;
    public const PHINK_CLIENT_TEMPLATE = 2;
    public const PHINK_WIDGET_TEMPLATE = 4;
    public const PHINK_PARTIAL_TEMPLATE = 8;
    public const PHINK_INNER_TEMPLATE = 16;
}
