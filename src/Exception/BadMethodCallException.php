<?php

declare(strict_types=1);

namespace CQRS\Exception;

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
}
