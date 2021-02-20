<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Ephect\CLI\Application;
use Ephect\IO\Utils;
use Ephect\Xml\XmlDocument;

class Program extends Application
{
    public static function main($argv, $argc)
    {
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {

        $str = <<<HTML
    <phx:Mother id="mother0" >
        <phx:Block id="title">Ephect in action !</phx:Block>
        <phx:Block id="stylesheets">
            <link rel="stylesheet" href="css/pond-theme.css" />
            <link rel="stylesheet" href="css/pond.css" />
        </phx:Block>
        <div class="App" >
        <phx:Block id="header" >
            <Header />
        </phx:Block>
        <phx:Block id="main" >
            <div class="App-content" >
                <phx:Ephect message='Hello World!' from="the app" />
            </div>
        </phx:Block>
        <phx:Block id="footer" >
            <phx:Footer id="footer" />
        </phx:Block>
        </div>
        <phx:Block id="javascripts" >
            <script src="js/pond.js"></script>
        </phx:Block>
    </phx:Mother>
HTML;

        $doc = new XmlDocument($str);
        $doc->matchAll();

        $list = $doc->getList();

        $json = json_encode($list, JSON_PRETTY_PRINT);

        Utils::safeWrite('doc.json', $json);
    }
}

Program::main($argv, $argc);
