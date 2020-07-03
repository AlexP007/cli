<?php


namespace Cli\Command;

use Cli\Basic\Formatter;
use RegexIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use Cli\Basic\Cli;
use Cli\Basic\Flags;
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
            ['-r', '-e', '-re'],
            []
        );
    }

    public static function run($path, $pattern, Flags $flags)
    {
        self::ensureArgument(is_string($path), 'file:find path should be string');
        self::ensureArgument(is_string($pattern), 'file:find pattern should be string');

        $path = realpath($path);
        self::ensureArgument($path !== false, 'file:find path should be a valid path');

        $result = [['Filename', 'Filepath']];

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $files = new RegexIterator($it, "/$pattern/", RegexIterator::MATCH);
        foreach ($files as $file) {
            $result[] = [
                $file->getFileName(),
                str_replace($path, '', $file->getPathName())
            ];
        }

        $fmt = new Formatter($result);
        return $fmt->asTable()->yellow();
    }
}