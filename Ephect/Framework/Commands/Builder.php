<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Templates\TemplateMaker;

class Builder
{
    /**
     * Third and last creation step of the User Command
     *
     * Read templates text, replace the markups and save into user application directory
     *
     * @param string $verb
     * @param string $subject
     * @param string $description
     * @param string $methodName
     * @param array $arguments
     * @param string $srcDir
     * @param string $destDir
     * @return void
     */
    function copyTemplates(string $verb, string $subject, string $description, string $methodName, array $arguments, string $srcDir, string $destDir): void
    {
        $commandNamespace = ucfirst($verb) . ucfirst($subject);
        $commandAttributes = 'verb: "' . $verb . '"';
        $commandAttributes = $subject !== "" ? $commandAttributes . ', subject: "' . $subject . '"' : $commandAttributes;
        $commandTemplatesDir = \Constants::EPHECT_ROOT . 'Templates' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR;

        $mainTextMaker = new TemplateMaker($commandTemplatesDir . 'Main.tpl');
        $libTextMaker = new TemplateMaker($commandTemplatesDir . 'Lib.tpl');

        $mainTextMaker->make([
            'ApplicationNamespace' => \Constants::CONFIG_NAMESPACE,
            'CommandNamespace' => $commandNamespace,
            'CommandAttributes' => $commandAttributes,
            'Description' => $description,
            'MethodName' => $methodName,
        ]);

        $libTextMaker->make([
            'ApplicationNamespace' => \Constants::CONFIG_NAMESPACE,
            'CommandNamespace' => $commandNamespace,
            'MethodName' => $methodName,
        ]);

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

            $mainTextMaker->make([
                'GetArgs' => $getargs,
                'SetArgs' => $setargs,
            ]);

            $libTextMaker->make([
                'Parameters' => $parameters,
            ]);
        } else {
            $mainTextMaker->make([
                'GetArgs' => '',
                'SetArgs' => '',
            ]);

            $libTextMaker->make([
                'Parameters' => '',
            ]);
        }

        $mainTextMaker->save($destDir . "Main.php");
        $libTextMaker->save($destDir . "Lib.php");
    }
}
