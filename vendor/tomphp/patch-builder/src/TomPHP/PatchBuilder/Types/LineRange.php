<?php

namespace TomPHP\PatchBuilder\Types;

use TomPHP\PatchBuilder\Types\Exception\InvalidLineNumberException;
use TomPHP\PatchBuilder\Types\Exception\InvalidLineRangeException;

class LineRange implements LineRangeInterface
{
    /**
     * @var LineNumber
     */
    private $start;

    /**
     * @var LineNumber
     */
    private $end;

    /**
     * @throws InvalidLineRangeException
     */
    public function __construct(LineNumber $start, LineNumber $end)
    {
        if ($start->getNumber() > $end->getNumber()) {
            throw InvalidLineRangeException::startGreaterThanEnd();
        }

        $this->start = $start;
        $this->end   = $end;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getLength()
    {
        return $this->end->getNumber() - $this->start->getNumber() + 1;
    }

    /**
     * @param int $lineNumber
     *
     * @return LineRange
     */
    public static function createSingleLine($lineNumber)
    {
        $number = new LineNumber($lineNumber);

        return new static($number, $number);
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return LineRange
     */
    public static function createFromNumbers($start, $end)
    {
        return new static(new LineNumber($start), new LineNumber($end));
    }
}
