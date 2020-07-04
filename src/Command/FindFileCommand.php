<?php


namespace Cli\Command;

use RegexIterator;

use Cli\Basic\Cli;
use Cli\Basic\Flags;
use Cli\Basic\Formatter;
use Cli\Helper\Directory;
use Cli\Traits\ArgumentThrower;

/**
 * Class FindFileCommand
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

    const COMMAND_NAME = 'find:file';

    public static function handle(array $data)
    {
        Cli::handle(
            self::COMMAND_NAME,
            [self::class, 'run'],
            ['-r'],
            []
        );
    }

    /**
     * Iterates over $path and searches for files matches pegExp $pattern.
     * If flag -r is specified then iterates recursively
     *
     * @param $realpath
     * @param $pattern
     * @param Flags $flags
     * @return Formatter
     * @throws \Cli\Exception\ArgumentException
     */
    public static function run($path, $pattern, Flags $flags)
    {
        self::ensureArgument(is_string($path), self::COMMAND_NAME . 'path should be string');
        self::ensureArgument(is_dir($path), self::COMMAND_NAME . 'path should be directory');
        self::ensureArgument(is_string($pattern), self::COMMAND_NAME . 'pattern should be string');

        $realpath = realpath($path);
        $path = rtrim($path, '/'); // for future concatenations

        self::ensureArgument($realpath !== false, self::COMMAND_NAME . 'path should be a valid path');

        $result = [['Filename', 'Filepath']];

        $it = Directory::getIterator($realpath, (bool) $flags->getFlag('-r'));
        $files = new RegexIterator($it, "/$pattern/", RegexIterator::MATCH);

        foreach ($files as $file) {
            $filepath = str_replace($realpath, '', $file->getPathName());
            $filepath = $path . '/' . ltrim($filepath, '/');
            $result[] = [$file->getFileName(), $filepath];
        }

        if (count($result) > 1) {
            $fmt = new Formatter($result);
            return $fmt->asTable()->yellow();
        } else {
            $fmt = new Formatter(self::COMMAND_NAME . ' -> find nothing');
            return $fmt->yellow();
        }
    }
}
