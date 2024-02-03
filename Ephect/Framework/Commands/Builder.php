<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\IO\Utils;

class Builder
{

    /**
     * Third and last creation step of the User Command
     *
     * Read templates text, replace the markups and save into user application directory
     *
     * @param string $verb
     * @param string $subject
     * @param string $methodName
     * @param array $arguments
     * @param string $srcDir
     * @param string $destDir
     * @return void
     */
    function copyTemplates(string $verb, string $subject, string $description, string $methodName, array $arguments, string $srcDir, string $destDir): void
    {
        $commandNamespace = ucfirst($verb)  . ucfirst($subject);
        $commandAttributes = 'verb: "' . $verb . '"';
        $commandAttributes = $subject !== "" ? $commandAttributes . ', subject: "' . $subject . '"' : $commandAttributes;

        $mainText = Utils::safeRead($srcDir . 'Main.tpl');
        $mainText = str_replace('{{CommandNamespace}}', $commandNamespace, $mainText);
        $mainText = str_replace('{{CommandAttributes}}', $commandAttributes, $mainText);
        $mainText = str_replace('{{Description}}', $description, $mainText);
        $mainText = str_replace('{{MethodName}}', $methodName, $mainText);

        $libText = Utils::safeRead($srcDir . 'Lib.tpl');
        $libText = str_replace('{{CommandNamespace}}', $commandNamespace, $libText);
        $libText = str_replace('{{MethodName}}', $methodName, $libText);

        if (count($arguments) > 0) {

            $getargs = '';
            $l = count($arguments);
            for ($i = 0; $i < $l; $i++) {
                $j = $i + 2;
                $getargs .= <<< ARGS
                \$$arguments[$i] = \$this->application->getArgi($j);\n
                ARGS;
                $getargs .= '        ';
            }

            $libParams = array_map(function ($property) {
                return "\$$property";
            }, $arguments);
            $setargs = implode(', ', $libParams);

            $properties = array_map(function ($property) {
                return "string \$$property";
            }, $arguments);

            $parameters = implode(', ', $properties);

            $mainText = str_replace('{{GetArgs}}', $getargs, $mainText);
            $mainText = str_replace('{{SetArgs}}', $setargs, $mainText);
            $libText = str_replace('{{Parameters}}', $parameters, $libText);
        } else {
            $mainText = str_replace('{{GetArgs}}','', $mainText);
            $mainText = str_replace('{{SetArgs}}', '', $mainText);
            $libText = str_replace('{{Parameters}}', '', $libText);
        }

        Utils::safeWrite($destDir . "Main.php", $mainText);
        Utils::safeWrite($destDir . "Lib.php", $libText);
    }
}