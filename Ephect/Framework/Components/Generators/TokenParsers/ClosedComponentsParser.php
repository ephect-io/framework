<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\WebComponents\ManifestReader;

final class ClosedComponentsParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $this->result = [];

        $comp = $this->component;
        $decl = $comp->getDeclaration();
        $cmpz = $decl->getComposition();

        if ($cmpz === null) {
            return;
        }

        $subject = $this->html;

        $closure = function (ComponentEntityInterface $item, int $index)  use (&$subject, &$result) {

            if ($item->hasCloser()) {
                return;
            }

            $uid = $item->getUID();
            $component = $item->getText();
            $componentName = $item->getName();
            $componentArgs = [];
            $componentArgs['uid'] = $uid;

            $args = '';
            if ($item->props() !== null) {
                $componentArgs = array_merge($componentArgs, $item->props());
                $args = json_encode($componentArgs, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
                $args = "json_decode('$args')";
            }

            $funcName = ComponentRegistry::read($componentName);
            $filename = ComponentRegistry::read($funcName);

            $componentRender = "\t\t\t<?php \$fn = \\{$funcName}($args); \$fn(); ?>\n";

            if ($filename === null) {
                $filename = WebComponentRegistry::read($funcName);
                if ($filename !== null) {

                    $reader = new ManifestReader($this->component->getMotherUID(), $componentName);
                    $manifest = $reader->read();
                    $tag = $manifest->getTag();
                    $text = str_replace($componentName, $tag, $component);
                    $text = str_replace('/>', '>', $text);
                    $text .=  '</' . $tag . '>';
                    Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $componentName . $uid . '.txt', $text);
                }
            }


            $subject = str_replace($component, $componentRender, $subject);

            array_push($this->result, $componentName);

            $filename = $this->component->getFlattenSourceFilename();
            Utils::safeWrite(CACHE_DIR . $this->component->getMotherUID() . DIRECTORY_SEPARATOR . $filename, $subject);
        };

        if (!$cmpz->hasChildren()) {
            $closure($cmpz, 0);
        }
        if ($cmpz->hasChildren()) {
            $cmpz->forEach($closure, $cmpz);
        }

        $this->html = $subject;
    }
}
