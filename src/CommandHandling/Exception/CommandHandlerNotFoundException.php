<?php

namespace CQRS\CommandHandling\Exception;

use Interop\Container\Exception\NotFoundException;

class CommandHandlerNotFoundException extends \RuntimeException implements ExceptionInterface, NotFoundException
{
}
