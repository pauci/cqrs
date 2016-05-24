<?php

namespace CQRSTest\EventHandling\Publisher;

use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use CQRSTest\Domain\Model\AggregateRootUnderTest;
use Ramsey\Uuid\Uuid;

class SimpleIdentityMapTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $identityMap = new SimpleIdentityMap();

        $ar1 = new AggregateRootUnderTest(Uuid::uuid4());
        $identityMap->add($ar1);

        $ar2 = new AggregateRootUnderTest(new \stdClass());
        $identityMap->add($ar2);
        $identityMap->add($ar2); // Add same object twice

        $this->assertSame([$ar1, $ar2], $identityMap->getAll());

        $identityMap->remove($ar1);

        $this->assertSame([$ar2], $identityMap->getAll());

        $identityMap->clear();

        $this->assertSame([], $identityMap->getAll());
    }
}
