<?php

$tag = 'Block';

$re = '/<(' . $tag . ')(\b[^>]*)>((?:(?>[^<]+)|<(?!\1\b[^>]*>))*?)(<\/\1>)/m';

$str = <<<HTML
namespace Fun;

function Home()
{
    return (
    <Mother>
        <Block name="title">FunCom in action !</Block>
        <Block name="stylesheets">
            <link rel="stylesheet" href="css/pond-theme.css" />
            <link rel="stylesheet" href="css/pond.css" />
        </Block>
        <div class="App" >
        <Block name="header" ><Header /></Block>
        <Block name="main">
            <div class="App-content" >
                <FunCom message='Hello World!' from="the app" />
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

preg_match_all($re, $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

// Print the entire match result
echo json_encode($matches, JSON_PRETTY_PRINT);

preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

echo json_encode($matches, JSON_PRETTY_PRINT);