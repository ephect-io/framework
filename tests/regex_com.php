<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Ephect\CLI\Application;
use Ephect\Components\Generators\ComponentParser;
use Ephect\Components\PreHtml;
use Ephect\IO\Utils;

class Program extends Application
{
    public static function main($argv, $argc)
    {
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {
        $str = <<<HTML
namespace Fun;

function Home()
{
    return (
    <Mother>
        <Block name="title">Ephect in action !</Block>
        <Block name="stylesheets">
            <link rel="stylesheet" href="css/pond-theme.css" />
            <link rel="stylesheet" href="css/pond.css" />
        </Block>
        <div class="App" >
        <Block name="header" ><Header /></Block>
        <Block name="main">
            <div class="App-content" >
                <Ephect message='Hello World!' from="the app" />
            </div>
        </Block>
        <Block name="footer" ><Footer /></Block>
        </div>
        <Block name="javascripts">
            <script src="js/pond.js"></script>
        </Block>
    </Mother>
    );
}
HTML;

        $prehtml = new PreHtml($str);
        $cp = new ComponentParser($prehtml);
        $matches = $cp->doComponents();

        $json = json_encode($matches, JSON_PRETTY_PRINT);

        Utils::safeWrite('doc2.json', $json);
    }
}

Program::main($argv, $argc);
