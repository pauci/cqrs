<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

interface EventHandlerLocatorInterface
{
    /**
     * @return array<callable>
     */
    public function get(string $eventType): array;
}
