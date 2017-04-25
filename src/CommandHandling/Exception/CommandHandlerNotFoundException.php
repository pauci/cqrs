<?php

namespace CQRS\CommandHandling\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class CommandHandlerNotFoundException extends RuntimeException implements ExceptionInterface, NotFoundExceptionInterface
{
}
