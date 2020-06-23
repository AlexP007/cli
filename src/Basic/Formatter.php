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

    /**
     * Formatter constructor.
     * @param $value
     */
    public function __construct($value)
    {
        if (is_array($value) ) {
            $value = json_encode($value, JSON_PRETTY_PRINT);
        }
        $this->value = $value;
    }

    /**
     * @return string
     *
     * Return colored $value
     */
    public function __toString(): string
    {
        $this->makeColored();

        return $this->value;
    }

    /**
     * Make $value colored
     */
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

    /**
     * Make $value red
     */
    private function makeRed()
    {
        $this->value = "\033[31m{$this->value}\033[0m";
    }

    /**
     * Make $value blue
     */
    private function makeBlue()
    {
        $this->value = "\033[34m{$this->value}\033[0m";
    }

    /**
     * Make value Yellow
     */
    private function makeYellow()
    {
        $this->value = "\033[93m{$this->value}\033[0m";
    }

    /**
     * @return $this
     *
     * Set output color to red
     */
    public function red()
    {
        $this->color = self::COLOR_RED;
        return $this;
    }

    /**
     * @return $this
     *
     * Set output color to blue
     */
    public function blue()
    {
        $this->color = self::COLOR_BLUE;
        return $this;
    }

    /**
     * @return $this
     *
     * Set output color to yellow
     */
    public function yellow()
    {
        $this->color = self::COLOR_YELLOW;
        return $this;
    }

    /**
     * @return $this
     *
     * Add line break
     */
    public function line()
    {
        $this->value .= "\n";
        return $this;
    }

    /**
     * Print this object as string
     */
    public function print()
    {
        echo $this;
    }
}
