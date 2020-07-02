<?php


namespace Cli\Helper;

use Cli\Traits\ArgumentThrower;

/**
 * Class Value
 * @package Cli/Helper
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class TableBuilder
{
    use ArgumentThrower;

    const SYMBOL_CORNER = '+';
    const SYMBOL_LINE_HORIZONTAL = '-';
    const SYMBOL_LINE_VERTICAL = '|';
    const SYMBOL_LINE_BREAK = "\n";
    const SYMBOL_SPACE = " ";

    const NUMBER_SPECIAL_SYMBOLS = 1;

    private $data = [];
    private $columns = [];
    private $columnsWidth = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validate();
        $this->setColumnsWidth();
    }

    private function validate()
    {
        foreach ($this->data as $line) {
            foreach ($line as $key => $column) {
                self::ensureArgument(is_string($column), "columns should be strings");
            }
        }
    }

    public function build(): string
    {
        $heading = $this->buildHeader(array_shift($this->data));
        $body = '';

        foreach ($this->data as $column) {
            $body .= $this->buildRow($column);
        }

        $footer = $this->buildHorizontalLine();

        return $heading . $body . $footer;
    }

    private function setColumnsWidth()
    {
        $result = [];
        foreach ($this->data as $line) {
            foreach ($line as $key => $column) {
                $len = strlen($column) + 2;
                if ($len > $result[$key]) {
                    $result[$key] = $len;
                }
            }
        }

        $this->columnsWidth = $result;
    }

    private function buildHeader(array $columns): string
    {
        $header = $this->buildHorizontalLine();
        $header .= $this->buildRow($columns);
        $header .= $this->buildHorizontalLine();

        return $header;
    }

    private function buildRow(array $columns): string
    {
        $row = '';
        foreach ($columns as $num => $column) {
            $row .= self::SYMBOL_LINE_VERTICAL . self::SYMBOL_SPACE;
            $row .= $column;
            $row .= str_repeat(
                self::SYMBOL_SPACE,
                $this->columnsWidth[$num] - self::NUMBER_SPECIAL_SYMBOLS - mb_strlen($column)
            );
        }
        $row .= self::SYMBOL_LINE_VERTICAL . self::SYMBOL_LINE_BREAK;

        return $row;
    }

    private function buildHorizontalLine(): string
    {
        $line = '';
        foreach ($this->columnsWidth as $columnWidth) {
            $line .= self::SYMBOL_CORNER;
            $line .= str_repeat(
                self::SYMBOL_LINE_HORIZONTAL,
                $columnWidth
            );
        }
        $line .= self::SYMBOL_CORNER . self::SYMBOL_LINE_BREAK;

        return $line;
    }
}