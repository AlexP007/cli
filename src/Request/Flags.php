<?php


namespace Request;

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
    /**
     * @var array
     */
    private $flags;

    public function __construct(array $requestFlags)
    {
        $this->setFlags($requestFlags);
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

    public function getFlagsAsArray(): array
    {
        return $this->flags;
    }
}