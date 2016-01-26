<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\Timestamp;

class TimestampTest extends \PHPUnit_Framework_TestCase
{
    private $tz;

    public function setUp()
    {
        $this->tz = date_default_timezone_get();
        date_default_timezone_set('Brazil/Acre');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->tz);
    }

    public function testMicroseconds()
    {
        $ts1 = new Timestamp();
        $ts2 = new Timestamp();

        $diff = $ts2->getTimestampWithMicroseconds() - $ts1->getTimestampWithMicroseconds();

        $this->assertGreaterThan(0, $diff);
        $this->assertLessThan(1, $diff);

        $this->assertEquals('Brazil/Acre', $ts1->getTimezone()->getName());
    }

    public function testToString()
    {
        $time = '2016-01-25T09:38:46.217288-01:00';

        $ts = new Timestamp($time);

        $this->assertEquals($time, (string) $ts);
    }

    public function testJsonSerialize()
    {
        $time = '2016-01-25T09:38:46.217288-01:00';

        $ts = new Timestamp($time);

        $this->assertEquals('"' . $time . '"', json_encode($ts));
    }
}
