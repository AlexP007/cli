<?php


namespace Helper;

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

    private $headings;
    private $data;
    private $columns;
    private $lines;
    private $width;

    private $columnsWidth = [];

    public function __construct(array $data)
    {
        $this->headings = $data[0];
        $this->data = $data;
        $this->columns = count($this->headings);
    }

    public function build(): string
    {
        $this->setColumnsWidth();
        $this->width = array_sum($this->columnsWidth) + $this->columns;

        $heading = $this->buildLine($this->headings, 3);
        $body = "";

        unset($this->data[0]);
        foreach ($this->data as $column) {
            $body .= $this->buildLine($column, 3);
        }

        $bottom = str_repeat("*", $this->width + 1);

        return $heading . $body . $bottom;
    }

    private function setColumnsWidth()
    {
        $result = [];
        foreach ($this->data as $line) {
            foreach ($line as $key => $column) {
                self::ensureArgument(is_string($column), "columns should be strings"); // todo validate method
                $len = strlen($column) + 2;
                if ($len > $result[$key]) {
                    $result[$key] = $len;
                }
            }
        }

        $this->columnsWidth = $result;
    }

    private function buildLine(array $columns, int $height): string
    {
        $line = str_repeat("*", $this->width + 1) . "\n";

        for($i = 0; $i < $height; ++$i) {
            foreach ($columns as $num => $column) {
                if ($i !== 1) {
                    $line .= "*";
                    $line .= str_repeat(" ", $this->columnsWidth[$num]);
                } else {
                    $line .= "* ";
                    $line .= "$column ";
                    $line .= str_repeat(" ", $this->columnsWidth[$num] - 2 - strlen($column));
                }
            }
            $line .= "*\n";
        }

        return $line;
    }
}