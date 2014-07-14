<?php

namespace CQRS;

use DateTimeImmutable;

class Util
{
    /**
     * @return DateTimeImmutable
     */
    public static function createMicrosecondsNow()
    {
        return DateTimeImmutable::createFromFormat('u', substr(microtime(), 2, 6));
    }
} 
