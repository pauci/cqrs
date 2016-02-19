<?php

namespace CQRS\Serializer\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class RamseyUuidHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];
        $types = [Uuid::class, DegradedUuid::class];

        foreach ($formats as $format) {
            foreach ($types as $type) {
                $methods[] = [
                    'type'      => $type,
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format'    => $format,
                    'method'    => 'serializeUuid'
                ];
            }

            $methods[] = [
                'type'      => Uuid::class,
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => $format,
                'method'    => 'deserializeUuid'
            ];
        }

        return $methods;
    }

    public function serializeUuid(VisitorInterface $visitor, UuidInterface $uuid, array $type, Context $context)
    {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }

    public function deserializeUuid(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        if (null === $data) {
            return null;
        }

        return Uuid::fromString($data);
    }
}
