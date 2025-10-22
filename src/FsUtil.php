<?php

namespace CrudeSSG;

class FsUtil
{
    /**
     * Ensure a directory exists
     */
    public static function ensureDir(string $path)
    {
        if (!is_dir($path)) {
            mkdir($path, recursive: true);
        }
    }

    /**
     * Recursively scan a directory and return all files
     */
    public static function rscandir(string $dir, array $files = [])
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $path = $dir . DIRECTORY_SEPARATOR . $object;
                    if (is_dir($path)) {
                        $files = self::rscandir($path, $files);
                    } else {
                        $files[] = $path;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Recursively remove a directory and its contents
     */
    public static function rrmdir(string $dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $path = $dir . DIRECTORY_SEPARATOR . $object;
                    if (is_dir($path)) {
                        self::rrmdir($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Recursively copy a directory
     */
    public static function rcopy(string $src, string $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, recursive: true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $srcPath = $src . DIRECTORY_SEPARATOR . $file;
                $dstPath = $dst . DIRECTORY_SEPARATOR . $file;
                if (is_dir($srcPath)) {
                    self::rcopy($srcPath, $dstPath);
                } else {
                    copy($srcPath, $dstPath);
                }
            }
        }
        closedir($dir);
    }
}