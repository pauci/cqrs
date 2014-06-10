<?php

namespace CQRS;

use DateTime;

class Util
{
    /**
     * @return DateTime
     */
    public static function createMicrosecondsNow()
    {
        return DateTime::createFromFormat('u', substr(microtime(), 2, 6));
    }
} 
