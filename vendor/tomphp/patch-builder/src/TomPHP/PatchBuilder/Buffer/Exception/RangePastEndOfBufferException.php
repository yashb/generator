<?php

namespace TomPHP\PatchBuilder\Buffer\Exception;

use TomPHP\PatchBuilder\Types\LineRangeInterface;

class RangePastEndOfBufferException extends \RangeException
{
    /**
     * @param int $bufferLength
     *
     * @return RangePastEndOfBufferException
     */
    public static function fromRange(LineRangeInterface $range, $bufferLength)
    {
        return new self(sprintf(
            'Range %d-%d goes beyond buffer with %d lines.',
            $range->getStart()->getNumber(),
            $range->getEnd()->getNumber(),
            $bufferLength
        ));
    }
}
