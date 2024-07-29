<?php

namespace Ephect\Apps\Builder\Copiers;

use Ephect\Framework\Utils\File;

class FilesCopier
{
    public function makeCopies(bool $unique = false)
    {
        if($unique) {
            File::safeMkDir(UNIQUE_DIR);
        } else {
            File::safeMkDir(COPY_DIR);
        }

        $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            if($unique) {
                $this->copyUniqueComponent(SRC_ROOT, $key, $compFile);
            } else {
                $this->copyComponent(SRC_ROOT, $key, $compFile);
            }
        }

        $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            if($unique) {
                $this->copyUniqueComponent(CUSTOM_PAGES_ROOT, $key, $pageFile);
            } else {
                $this->copyComponent(CUSTOM_PAGES_ROOT, $key, $pageFile);
            }
        }

        $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
        foreach ($componentsList as $key => $compFile) {
            if($unique) {
                $this->copyUniqueComponent(CUSTOM_COMPONENTS_ROOT, $key, $compFile);
            } else {
                $this->copyComponent(CUSTOM_COMPONENTS_ROOT, $key, $compFile);
            }
        }
    }

    public function copyComponent(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(COPY_DIR . $root . $dirname);
        copy($path . $dirname . $basename, COPY_DIR . $root . $dirname . $basename);
    }

    public function copyUniqueComponent(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(UNIQUE_DIR . $root . $dirname);
        $contents = file_get_contents($path . $dirname . $basename);

        copy($path . $dirname . $basename, UNIQUE_DIR . $root . $dirname . $basename);
    }
}