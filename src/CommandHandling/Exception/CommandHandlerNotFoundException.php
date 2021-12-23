<?php

declare(strict_types=1);

namespace CQRS\CommandHandling\Exception;

use RuntimeException;

class CommandHandlerNotFoundException extends RuntimeException implements ExceptionInterface
{
}
