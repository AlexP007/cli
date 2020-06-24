<?php


namespace Cli\Domain;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Flag
{
    /**
     * @var string
     */
    private $flag;

    /**
     * @var string
     */
    private $value;

    /**
     * Flag constructor.
     *
     * @param string $flag
     */
    public function __construct(string $flag)
    {
        $this->setFlag($flag);
    }

    /**
     * @param string $flag
     */
    private function setFlag(string $flag)
    {
        if (strstr($flag, '=')) {
            $splitFlag = explode('=', $flag);
            $this->flag = $splitFlag[0];
            $this->value = $splitFlag[1];
        } else {
            $this->flag = $flag;
            $this->value = true;
        }

    }

    /**
     * @return string
     */
    public function getFlag(): string
    {
        return $this->flag;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check if string could be split into valid flag
     *
     * @param string $value
     * @return false|int
     */
    public static function isFlag(string $value): bool
    {
        return  preg_match('/^-{1,2}\w/', $value);
    }
}
