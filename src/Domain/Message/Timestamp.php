<?php

namespace CQRS\Domain\Message;

use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;

class Timestamp extends DateTimeImmutable implements JsonSerializable
{
    const FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @param string $time
     * @param DateTimeZone|null $timezone
     */
    public function __construct($time = null, DateTimeZone $timezone = null)
    {
        if ($time === null) {
            $t = microtime(true);
            $micro = sprintf('%06d', ($t - floor($t)) * 1000000);
            $time = date('Y-m-d H:i:s.' . $micro, $t);
        }
        parent::__construct($time, $timezone);
    }

    /**
     * @return float
     */
    public function getTimestampWithMicroseconds()
    {
        return (float) $this->format('U.u');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format(static::FORMAT);
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }
}
