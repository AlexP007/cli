<?php


namespace Request;

use Traits\Thrower;

/**
 * Class Value
 * @package Cli/Request
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Flags
{
    use Thrower;
    /**
     * @var array
     */
    private $flags;

    public function __construct(array $requestFlags, array $commandFlags)
    {
        $this->setFlags($requestFlags);
        $this->checkFlags($commandFlags);
    }

    private function setFlags(array $requestFlags)
    {
        $result = [];
        foreach ($requestFlags as $flag) {
            if (strstr($flag, '=')) {
                $splitFlag = explode('=', $flag);
                $result[$splitFlag[0]] = $splitFlag[1];
            } else {
                $result[$flag] = true;
            }
        }

        $this->flags = $result;
    }

    private function checkFlags(array $commandFlags)
    {
        $flags = array_keys($this->flags);
        $diff = array_diff($flags, $commandFlags);

        self::ensureArgument(
            count($diff) < 1,
            join(', ', $diff) . " are not allowed flags for this command"
        );
    }
}