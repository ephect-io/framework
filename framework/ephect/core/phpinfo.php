<?php

namespace Ephect\Core;

use Ephect\Registry\Registry;
use stdClass;

final class PhpInfo
{
    public static function getGeneralSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_GENERAL, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getConfigurationSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_CONFIGURATION, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getModulesSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_MODULES, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getEnvironmentSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_ENVIRONMENT, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getPhpVariablesSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_VARIABLES, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getPhpCreditstSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_CREDITS, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getLicenseSection(bool $asArray = false)
    {
        $section = self::getSection(INFO_LICENSE, $asArray);
        return ($asArray) ? $section['section']['values'] : $section->section->values;
    }

    public static function getSection(int $section, bool $asArray = false)
    {

        if(Registry::exists('ini', $section)) {
            return Registry::read('ini', $section);
        }

        $root = [];
        $cat = 'general';

        if (!$asArray) {
            $root = new stdClass;
            $root->section = new stdClass;
            $root->section->name = $cat;
            $root->section->values = new stdClass;
        }

        ob_start();
        phpinfo($section);
        $lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));

        foreach ($lines as $line) {

            if (false !== preg_match("~<h2>(.*)</h2>~", $line, $title) ? (isset($title[1]) ? $cat = $title[1] : false) : false) {
                // new cat?
                $cat = self::_formatKey($cat);
                if (!$asArray) {
                    $root->section = new stdClass;
                    $root->section->name = $cat;
                    $root->section->values = new stdClass;
                }

                if ($asArray) {
                    $root['section'] = [];
                    $root['section']['name'] = $cat;
                    $root['section']['values'] = [];
                }
            }

            if (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                $key = self::_formatKey($val[1]);
                $value = self::_formatValue($val[2]);

                if ($cat !== 'php_variables') {
                    if (!$asArray) {
                        $root->section->values->$key = $value;
                    }
                    if ($asArray) {
                        $root['section']['values'][$key] = $value;
                    }
                }

                if ($cat == 'php_variables') {
                    if (preg_match('~\$_(server|cookie)\[\'([a-z_]*)\'\]~', $key, $val)) {
                        $subcat = $val[1];
                        $subkey = $val[2];

                        if (!$asArray) {
                            if (!property_exists($root->section->values, $subcat)) {
                                $root->section->values->$subcat = new stdClass;
                            }
                            $root->section->values->$subcat->$subkey = $value;
                        }

                        if ($asArray) {
                            $root['section']['values'][$subcat][$subkey] = $value;
                        }
                    }
                }
            } elseif (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                $key = self::_formatKey($val[1]);
                $local = self::_formatValue($val[2]);
                $master = self::_formatValue($val[3]);

                if (!$asArray) {
                    $root->section->values->$key = new stdClass;
                    $root->section->values->$key->local = $local;
                    $root->section->values->$key->master = $master;
                }

                if ($asArray) {
                    $root['section']['values'][$key] = ['local' => $local, 'master' => $master];
                }
            }
        }
        Registry::write('ini', $section, $root);

        return $root;
    }

    public static function displaySection(int $infoSection, bool $asJSON = false): void
    {
        $array = self::getSection($infoSection);

        if ($asJSON) {
            echo '<pre>' . PHP_EOL;
            echo json_encode($array, JSON_PRETTY_PRINT);
            echo '</pre>' . PHP_EOL;

            return;
        }

        foreach ($array as $section => $data) {
            echo '<p>' . $section . '</p> ' . PHP_EOL;
            echo '<ul>' . PHP_EOL;
            foreach ($data as $key => $value) {
                if (!is_array($value)) {
                    echo '<li>' . $key . '= ' . $value . '</li> ' . PHP_EOL;
                }
                if (is_array($value)) {
                    echo '<li>' . PHP_EOL;
                    echo '<p>' . $key . '</p> ' . PHP_EOL;
                    echo '<ul>' . PHP_EOL;
                    foreach ($value as $subkey => $subvalue) {
                        echo '<li>' . $subkey . '= ' . $subvalue . '</li> ' . PHP_EOL;
                    }
                    echo '</ul>' . PHP_EOL;
                    echo '</li>' . PHP_EOL;
                }
            }
            echo '</ul>' . PHP_EOL;
        }
    }

    private static function _formatKey(string $key): string
    {
        $key = trim($key);
        $key = str_replace('(', '', $key);
        $key = str_replace(')', '', $key);
        $key = str_replace('/', '_', $key);
        $key = str_replace(' ', '_', $key);
        $key = str_replace('__', '_', $key);
        $key = strtolower($key);

        return $key;
    }

    private static function _formatValue(string $value): string
    {
        $value = trim($value);
        // $value = str_replace("\/", '/', $value);

        return $value;
    }
}
