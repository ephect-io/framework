<?php

namespace Ephect\Modules\WebApp\Builder\Descriptors;

use Constants;
use Ephect\Framework\Logger\Logger;
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
        $relativeDir = str_replace(\Constants::EPHECT_ROOT, '', $sourceDir);
        File::safeCopy($sourceDir . $filename, \Constants::COPY_DIR . $relativeDir . $filename);

        $manifestDir = realpath($this->modulePath . DIRECTORY_SEPARATOR . \Constants::REL_CONFIG_DIR);
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

        $parser = ParserFactory::createParser($moduleEntrypoint, $relativeDir . $filename);

        return $parser->parse();

    }
}