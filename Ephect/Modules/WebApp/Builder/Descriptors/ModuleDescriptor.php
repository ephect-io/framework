<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Ephect\Framework\Modules\ModuleManifestReader;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\WebApp\Builder\Parsers\ParserFactory;
use Exception;

class ModuleDescriptor implements DescriptorInterface
{
    public function __construct(private readonly string $modulePath)
    {
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     * @throws Exception
     */
    public function describe(string $sourceDir, string $filename): array
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $manifestDir = realpath($this->modulePath . DIRECTORY_SEPARATOR . REL_CONFIG_DIR);
        $manifestDir = is_dir($manifestDir) ? $manifestDir : $this->modulePath;

        $reader = new ModuleManifestReader();
        $manifest = $reader->read($manifestDir);

        $moduleEntrypoint = $manifest->getEntrypoint();

        if ($moduleEntrypoint == null) {
            return [null, null];
        }

        if (!in_array(ComponentInterface::class, class_implements($moduleEntrypoint))) {
            throw new Exception("Module entry point must implement " . ComponentInterface::class . " or be null.");
        }

        $parser = ParserFactory::createParser($moduleEntrypoint, $filename);

        return $parser->parse();

    }
}