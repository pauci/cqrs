<?php

namespace CQRS\Common;

use DateTime;
use DateTimeImmutable;

class MicrosecondsDateTimeFactory
{
    /**
     * @return DateTime
     */
    public static function createNow()
    {
        return DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)));
    }

    /**
     * @return DateTimeImmutable
     */
    public static function createImmutableNow()
    {
        return DateTimeImmutable::createFromFormat('U.u', sprintf('%.f', microtime(true)));
    }
} 
