<?php

namespace CQRS\Common;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class MicrosecondsDateTimeFactory
{
    /**
     * @return DateTime
     */
    public static function createNow()
    {
        $now = DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)));
        $now->setTimezone(new DateTimeZone(date_default_timezone_get()));
        return $now;
    }

    /**
     * @return DateTimeImmutable
     */
    public static function createImmutableNow()
    {
        return DateTimeImmutable::createFromMutable(self::createNow());
    }
} 
