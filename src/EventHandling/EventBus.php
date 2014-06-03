<?php

namespace CQRS\EventHandling;

interface EventBus
{
    public function publish(DomainEvent $event);
}
