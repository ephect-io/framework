<?php

namespace Ephect\Utils;

use ZipArchive;

class Zip
{
    //put your code here
    function inflate($src_file, $dest_dir = false, $create_zip_name_dir = true, $overwrite = true)
    {
        $zip = new ZipArchive;

        if (!$zip->open($src_file)) {
            return false;
        }

        $splitter = ($create_zip_name_dir === true) ? "." : "/";
        if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter)) . "/";

        // Create the directories to the destination dir if they don't already exist
        if (!file_exists($dest_dir)) {
            \mkdir($dest_dir, 0777, true);
        }

        // For every file in the zip-packet
        for ($i = 0; $i < $zip->numFiles; $i++) {

            $filename = $zip->statIndex($i)['name'];
            $pos_last_slash = strrpos($filename, "/");
            if ($pos_last_slash !== false) {
                // Create the directory where the zip-entry should be saved (with a "/" at the end)
                $path = $dest_dir . substr($filename, 0, $pos_last_slash + 1);
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
            }

            $zip->extractTo($dest_dir, $filename);
        }
        $zip->close();

        return true;
    }
}
