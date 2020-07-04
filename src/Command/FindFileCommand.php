<?php


namespace Cli\Command;

use RegexIterator;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use Cli\Basic\Cli;
use Cli\Basic\Flags;
use Cli\Basic\Formatter;
use Cli\Traits\ArgumentThrower;

/**
 * Class Value
 * @package Cli/Command
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class FindFileCommand extends Command
{
    use ArgumentThrower;

    public static function handle(array $data)
    {
        Cli::handle(
            'find:file',
            ['Cli\Command\FindFileCommand', 'run'],
            ['-r'],
            []
        );
    }

    /**
     * Iterates over $path and searches for files matches pegExp $pattern.
     * If flag -r is specified then iterates recursively
     *
     * @param $path
     * @param $pattern
     * @param Flags $flags
     * @return Formatter
     * @throws \Cli\Exception\ArgumentException
     */
    public static function run($path, $pattern, Flags $flags)
    {
        self::ensureArgument(is_string($path), 'file:find path should be string');
        self::ensureArgument(is_string($pattern), 'file:find pattern should be string');

        $path = realpath($path);
        self::ensureArgument($path !== false, 'file:find path should be a valid path');

        $result = [['Filename', 'Filepath']];

        $it = $flags->getFlag('-r')
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
            : new DirectoryIterator($path);

        $files = new RegexIterator($it, "/$pattern/", RegexIterator::MATCH);
        foreach ($files as $file) {
            $result[] = [
                $file->getFileName(),
                str_replace($path, '', $file->getPathName())
            ];
        }

        if (count($result) > 1) {
            $fmt = new Formatter($result);
            return $fmt->asTable()->yellow();
        } else {
            $fmt = new Formatter('find nothing');
            return $fmt->yellow();
        }
    }
}
