<?php

namespace CQRS\Serializer\Jms;

use CQRS\Domain\Message\Metadata;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

class MetadataHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];

        foreach ($formats as $format) {
            $methods[] = [
                'type'      => Metadata::class,
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => $format,
                'method'    => 'serializeMetadata'
            ];

            $methods[] = [
                'type'      => Metadata::class,
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => $format,
                'method'    => 'deserializeMetadata'
            ];
        }

        return $methods;
    }

    public function serializeMetadata(VisitorInterface $visitor, Metadata $metadata, array $type, Context $context)
    {
        return $visitor->visitArray($metadata->toArray(), $type, $context);
    }

    public function deserializeMetadata(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        if (null === $data) {
            return null;
        }

        return Metadata::from($data);
    }
}
