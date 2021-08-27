<?php

namespace Lib;

class DateTime extends \DateTime
{
    public const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    public function __toString()
    {
        return $this->format(self::DEFAULT_FORMAT);
    }
}