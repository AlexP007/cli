<?php


namespace Cli\Helper;

use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Directory
 * @package Cli/Helper
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Directory
{
    public static function getIterator(string $path, bool $recursive)
    {
        if ($recursive) {
            return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        }
        return new DirectoryIterator($path);
    }
}