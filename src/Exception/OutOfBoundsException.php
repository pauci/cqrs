<?php

declare(strict_types=1);

namespace CQRS\Exception;

class OutOfBoundsException extends \OutOfBoundsException implements ExceptionInterface
{
}
