<?php

namespace CQRS\Eventing;

interface EventBus
{
    public function publish(DomainEvent $event);
}
