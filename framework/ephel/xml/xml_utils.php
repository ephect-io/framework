<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ephel\Xml;

/**
 * Description of xml
 *
 * @author David
 */
class XmlUtils
{
        //put your code here
        public static function convertArray(array $array, string $node_block = 'nodes', string $node_name = 'node'): string
        {
                $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

                $xml .= '<' . $node_block . '>' . "\n";
                $xml .= self::convertArrayEx($array, $node_name);
                $xml .= '</' . $node_block . '>' . "\n";

                return $xml;
        }

        private static function convertArrayEx(array $array, string $node_name): string
        {
                $xml = '';

                if (is_array($array) || is_object($array)) {
                        foreach ($array as $key => $value) {
                                if (is_numeric($key)) {
                                        $key = $node_name;
                                }

                                $xml .= '<' . $key . '>' . "\n" . self::convertArrayEx($value, $node_name) . '</' . $key . '>' . "\n";
                        }
                } else {
                        $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
                }

                return $xml;
        }
}
