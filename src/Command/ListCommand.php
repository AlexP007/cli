<?php


namespace Cli\Command;

use Cli\Basic\Flags;
use Cli\Basic\Params;
use Cli\Basic\Environment;
use Cli\Basic\Formatter;
use Cli\Reflections\CommandReflection;

/**
 * Class Value
 * @package Cli/Command
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class ListCommand
{
    /**
     * Iterates thru all handled command
     * and returned summary
     *
     * @param Environment $env
     * @throws \ReflectionException
     */
    public static function run(Environment $env)
    {
        $handlers = $env->getEnv('handlers')->asArray();
        $header = ['Command', 'Params', 'Flags'];
        $body = [];

        foreach ($handlers as $command) {
            $commandReflection = new CommandReflection($command);
            $parameters = [];

            foreach ($commandReflection->getParameters() as $param) {
                $class = $param->getClass();

                // if use params, then no validation
                if ($class && $class->getName() === Params::class) {
                    continue;
                }
                // if with flags, we are not count this argument
                if ($class && $class->getName() === Flags::class) {
                    continue;
                }
                // if with env, we are not count last this argument
                if ($class && $class->getName() === Environment::class) {
                    continue;
                }
                $parameters[] = $param->getName();
            }
            $body[] = [
                $command->getName(),
                join(', ', $parameters),
                join(', ', $command->getFlags()),
            ];
        }

        usort($body, function ($a, $b) {
            return $a[0] > $b[0];
        });

        $output = array_merge([$header], $body);

        return (new Formatter($output))->asTable()->yellow();
    }
}
