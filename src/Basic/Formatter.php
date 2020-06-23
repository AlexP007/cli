<?php


namespace Cli\Basic;

/**
 * Class Value
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Formatter
{
    const COLOR_RED = 'red';
    const COLOR_BLUE = 'blue';
    const COLOR_YELLOW = 'yellow';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $color;

    public function __construct($value)
    {
        if (is_array($value) ) {
            $value = json_encode($value);
        }
        $this->value = $value;
    }

    public function __toString(): string
    {
        $this->makeColored();

        return $this->value;
    }

    private function makeColored()
    {
        switch ($this->color) {
            case self::COLOR_RED:
                $this->makeRed();
                break;
            case self::COLOR_BLUE:
                $this->makeBlue();
                break;
            case self::COLOR_YELLOW:
                $this->makeYellow();
                break;
        }
    }

    private function makeRed()
    {
        $this->value = "\033[31m{$this->value}\033[0m";
    }

    private function makeBlue()
    {
        $this->value = "\033[34m{$this->value}\033[0m";
    }
    private function makeYellow()
    {
        $this->value = "\033[93m{$this->value}\033[0m";
    }

    public function red()
    {
        $this->color = self::COLOR_RED;
        return $this;
    }

    public function blue()
    {
        $this->color = self::COLOR_BLUE;
        return $this;
    }

    public function yellow()
    {
        $this->color = self::COLOR_YELLOW;
        return $this;
    }

    public function line()
    {
        $this->value .= "\n";
        return $this;
    }
}
