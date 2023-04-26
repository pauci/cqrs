<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling\Publisher;

use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use CQRSTest\Domain\Model\SomeAggregateRoot;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SimpleIdentityMapTest extends TestCase
{
    public function test(): void
    {
        $identityMap = new SimpleIdentityMap();

        $ar1 = new SomeAggregateRoot(Uuid::uuid4());
        $identityMap->add($ar1);

        $ar2 = new SomeAggregateRoot(new \stdClass());
        $identityMap->add($ar2);
        $identityMap->add($ar2); // Add same object twice

        self::assertSame([$ar1, $ar2], $identityMap->getAll());

        $identityMap->remove($ar1);

        self::assertSame([$ar2], $identityMap->getAll());

        $identityMap->clear();

        self::assertSame([], $identityMap->getAll());
    }
}
