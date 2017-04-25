<?php

namespace CQRSTest\CommandHandling;

use CQRS\Common\MicrosecondsDateTimeFactory;
use PHPUnit_Framework_TestCase;

class MicrosecondsDateTimeFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testCreateCurrentTime()
    {
        MicrosecondsDateTimeFactory::createImmutableNow();
        MicrosecondsDateTimeFactory::createNow();
    }
}