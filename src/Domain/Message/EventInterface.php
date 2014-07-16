<?php

namespace CQRS\Domain\Message;

use DateTimeInterface;
use Rhumsaa\Uuid\Uuid;

interface EventInterface extends MessageInterface
{
    /**
     * @return Uuid
     */
    public function getId();

    /**
     * @return string
     */
    public function getEventName();

    /**
     * @return DateTimeInterface
     */
    public function getTimestamp();
} 
