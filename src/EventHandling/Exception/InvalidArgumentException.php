<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
