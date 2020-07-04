<?php


namespace Cli\Command;

use Cli\Basic\Cli;
use Cli\Basic\Flags;
use Cli\Basic\Formatter;
use Cli\Helper\Directory;
use Cli\Traits\ArgumentThrower;

/**
 * Class FindInFileCommand
 * @package Cli/Command
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class FindInFileCommand extends Command
{
    use ArgumentThrower;

    const COMMAND_NAME = 'find:inFile';

    public static function handle(array $data)
    {
        Cli::handle(
            self::COMMAND_NAME,
            [self::class, 'run'],
            ['-r', '-a', '-ra'],
            []
        );
    }

    public static function run($path, $pattern, Flags $flags)
    {
        self::ensureArgument(is_string($path), self::COMMAND_NAME . 'path should be string');
        self::ensureArgument(is_dir($path), self::COMMAND_NAME . 'path should be directory');
        self::ensureArgument(is_string($pattern), self::COMMAND_NAME . 'pattern should be string');

        $realpath = realpath($path);
        $path = rtrim($path, '/'); // for future concatenations

        self::ensureArgument($realpath !== false, self::COMMAND_NAME . 'path should be a valid path');

        $result = [['Match', 'Line', 'Filename', 'Filepath']];

        $files = Directory::getIterator($realpath, (bool) $flags->getFlag('-r'));

        foreach ($files as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $fileArray = file($filePath, FILE_IGNORE_NEW_LINES);
                $match = preg_grep("/$pattern/", $fileArray);

                if (count($match) > 0) {
                    $resultPath = str_replace($realpath, '', $filePath);
                    $resultPath = $path . '/' . ltrim($resultPath, '/');
                    foreach ($match as $lineNum => $string) {
                        $result[] = [trim($string), (string) ++$lineNum, $file->getFilename(), $resultPath];
                    }
                }
            }
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