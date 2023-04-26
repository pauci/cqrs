<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Model\AbstractAggregateRoot;

/**
 * @phpstan-template Id
 */
class SomeAggregateRoot extends AbstractAggregateRoot
{
    /**
     * @phpstan-var Id
     * @var mixed
     */
    private mixed $id;

    /**
     * @phpstan-param Id $id
     * @param mixed $id
     */
    public function __construct(mixed $id)
    {
        $this->id = $id;
    }

    /**
     * @phpstan-return Id
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    public function raise(object $event): void
    {
        $this->registerEvent($event);
    }
}
