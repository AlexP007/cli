<?php


namespace Cli\Command;

use ReflectionFunction;
use ReflectionMethod;

use Cli\Basic\Flags;
use Cli\Basic\Params;
use Cli\Basic\Environment;
use Cli\Basic\Formatter;

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
        $result = [];

        foreach ($handlers as $command) {
            $callable = $command->getCallable();
            if (is_array($callable) ) {
                $commandReflection = new ReflectionMethod($callable[0], $callable[1]);
            } else {
                $commandReflection = new ReflectionFunction($callable);
            }
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

            $result[] = [$command->getName(), $parameters, $command->getFlags()];
        }

        usort($result, function ($a, $b) {
            return $a[0] > $b[0];
        });


        // prepare for output
        $output = '';

        foreach ($result as $i) {
            $params = join(', ' ,$i[1]);
            $flags = join(', ' ,$i[2]);
            $output .= $i[0] . ": " . "params: [$params] ; flags: [$flags] \n";
        }

        return (new Formatter($output))->yellow();
    }
}