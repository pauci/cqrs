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
        return DateTime::createFromFormat('U.u', sprintf('%.f', microtime(true)))
            ->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }

    /**
     * @return DateTimeImmutable
     */
    public static function createImmutableNow()
    {
        return DateTimeImmutable::createFromFormat('U.u', sprintf('%.f', microtime(true)))
            ->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }
}
