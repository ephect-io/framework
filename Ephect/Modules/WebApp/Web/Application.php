<?php

namespace Ephect\Modules\WebApp\Web;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Core\AbstractApplication;
use Ephect\Framework\Core\ConstantsMaker;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\HooksRegistry;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Generators\TokenParsers\FragmentsParser;
use Ephect\Modules\Forms\Registry\CacheRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Ephect\Modules\WebApp\Services\BuildService;

class Application extends AbstractApplication
{
    private string $html = '';

    public static function create(...$params): self
    {
        self::$instance = new Application();
        self::$instance->run(...$params);

        return self::$instance;
    }

    public function run(...$params): int
    {
        $this->loadInFile();
        StateRegistry::load();
        if (!ComponentRegistry::load()) {
            $service = new BuildService();
            $service->build();
        }

        CacheRegistry::load();
        PluginRegistry::load();
        HooksRegistry::register(\Constants::APP_ROOT);
        FrameworkRegistry::load();
        FrameworkRegistry::registerBuiltComponents();
        FrameworkRegistry::save();

        $this->execute();

        return 0;
    }

    /**
     * @throws \ReflectionException
     */
    protected function execute(): int
    {
        $app = new Component('App');

        //        ob_start();
        $app->render();
        //        $this->html = ob_get_clean();

        return 0;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function displayConstants(): array
    {
        $maker = new ConstantsMaker();
        $constants = $maker->list();
        StateRegistry::writeItem('console', ['buffer' => $constants]);

        Console::Log('Application constants are :');
        $maker->log();

        return $constants;
    }


}
