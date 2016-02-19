<?php

namespace CQRS\EventHandling\Publisher;

interface EventPublisherInterface
{
    public function publishEvents();
}
