<?php

namespace CQRSTest\EventStore;

use CQRS\EventHandling\EventInterface;
use JMS\Serializer\Annotation as JMS;

class SomeEvent implements EventInterface
{
}
