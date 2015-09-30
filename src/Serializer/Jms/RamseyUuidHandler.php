<?php

namespace CQRS\Serializer\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Ramsey\Uuid\Uuid;

class RamseyUuidHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];

        foreach ($formats as $format) {
            $methods[] = [
                'type'      => Uuid::class,
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => $format,
                'method'    => 'serializeUuid'
            ];

            $methods[] = array(
                'type'      => Uuid::class,
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => $format,
                'method'    => 'deserializeUuid'
            );
        }

        return $methods;
    }

    public function serializeUuid(VisitorInterface $visitor, Uuid $uuid, array $type, Context $context)
    {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }

    public function deserializeUuid(VisitorInterface $visitor, $data,  array $type, Context $context)
    {
        if (null === $data) {
            return null;
        }

        return Uuid::fromString($data);
    }
}
