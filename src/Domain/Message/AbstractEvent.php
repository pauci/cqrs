<?php

namespace CQRS\Domain\Message;

use CQRS\Exception\RuntimeException;
use CQRS\Util;
use DateTimeImmutable;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractEvent extends AbstractMessage implements EventInterface
{
    /** @var Uuid */
    private $id;

    /** @var DateTimeImmutable */
    private $timestamp;

    /** @var string */
    private $eventName;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->id        = Uuid::uuid4();
        $this->timestamp = Util::createMicrosecondsNow();

        parent::__construct($data);
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        if (!$this->eventName) {
            $this->setEventName($this->parseEventName());
        }
        return $this->eventName;
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on event "%s"',
            $name,
            $this->getEventName()
        ));
    }

    /**
     * @param string $eventName
     */
    protected function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return string
     */
    private function parseEventName()
    {
        $name = get_class($this);

        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }

        if (substr($name, -5) === 'Event') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }
}
