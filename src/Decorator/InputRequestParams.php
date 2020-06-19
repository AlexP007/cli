<?php


namespace Decorator;

/**
 * Class Value
 * @package Cli/Decorator
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class InputRequestParams extends InputRequest
{
    public function process(): array
    {
        return [
            "request" => $this->getRequest()
        ];
    }
}
