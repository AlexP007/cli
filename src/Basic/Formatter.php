<?php


namespace Cli\Basic;

use Cli\Helper\TableBuilder;
use Cli\Traits\ArgumentThrower;

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
    use ArgumentThrower;

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
        $this->value = $value;
    }

    /**
     * Return colored $value
     *
     * @return string
     */
    public function __toString(): string
    {
        if (is_array($this->value) ) {
            $this->value = json_encode($this->value, JSON_PRETTY_PRINT);
        }
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
     * Set color to red
     *
     * @return $this
     */
    public function red()
    {
        $this->color = self::COLOR_RED;
        return $this;
    }

    /**
     * Set color to blue
     *
     * @return $this
     */
    public function blue()
    {
        $this->color = self::COLOR_BLUE;
        return $this;
    }

    /**
     * Set color to yellow
     *
     * @return $this
     */
    public function yellow()
    {
        $this->color = self::COLOR_YELLOW;
        return $this;
    }

    /**
     * Add line break
     *
     * @return $this
     */
    public function line()
    {
        $this->value .= "\n";
        return $this;
    }

    /**
     * Print this
     */
    public function printOut()
    {
        echo $this;
    }

    public function asTable()
    {
        self::ensureArgument(
            is_array($this->value),
            "formatter value should be array to prepare table output"
        );
        $tableBuilder = new TableBuilder($this->value);
        $this->value = $tableBuilder->build();

        return $this;
    }
}
