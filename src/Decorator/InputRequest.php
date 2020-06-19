<?php


namespace Decorator;

use Request\Request;

/**
 * Class Value
 * @package Cli/Decorator
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class InputRequest extends Decorator
{
    private $request;

    final public function __construct(Request $request)
    {
        $this->request = $request;
    }

    final public function getRequest(): Request
    {
        return $this->request;
    }
}