<?php

namespace Ephect\WebApp\Builder\Strategy;

use DateTime;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Web\Curl;
use Ephect\Modules\Routing\RouterService;
use Exception;

class BuildByRouteStrategy implements BuiderStrategyInterface
{
    /**
     * @throws Exception
     */
    public function build($route = 'Default'): void
    {

        $port = trim(File::safeRead(CONFIG_DIR . 'dev_port') ?? '80');

        if ($route === 'App') {
            return;
        }

        $queryString = $route === 'Default' ? '/' : RouterService::findRouteQueryString($route);
        if ($queryString === null) {
            return;
        }

        $filename = "$route.html";
        $outputFilename = "$route.out";

        Console::write("Compiling %s, ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
        Console::write("querying %s ... ", ConsoleColors::getColoredString(CONFIG_HOSTNAME . ":$port" . $queryString, ConsoleColors::LIGHT_GREEN));

        Console::getLogger()->info("Compiling %s ...", $route);


        $curl = new Curl();
        $time_start = microtime(true);

        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
        $headers[] = 'Accept-Encoding: gzip, deflate, br';
        $headers[] = 'Connection: keep-alive';
        if (isset($_COOKIE['PHPSESSID'])) {
            $headers[] = "Cookie: PHPSESSID={$_COOKIE['PHPSESSID']};";
        }
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'Sec-Fetch-Dest: document';
        $headers[] = 'Sec-Fetch-Mode: navigate';
        $headers[] = 'Sec-Fetch-Site: cross-site';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';

        ob_start();
        [$code, $header, $html] = $curl->request(CONFIG_HOSTNAME . ":$port" . $queryString, $headers);
        File::safeWrite(STATIC_DIR . $filename, $html);
        $output = ob_get_clean();
        File::safeWrite(LOG_PATH . $outputFilename, $output);

        $time_end = microtime(true);

        $duration = $time_end - $time_start;

        $utime = sprintf('%.3f', $duration);
        $raw_time = DateTime::createFromFormat('u.u', $utime);
        $duration = substr($raw_time->format('u'), 0, 3);

        Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
    }
}