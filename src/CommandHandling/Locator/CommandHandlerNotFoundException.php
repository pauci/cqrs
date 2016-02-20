<?php

namespace CQRS\CommandHandling\Locator;

use Interop\Container\Exception\NotFoundException;
use RuntimeException;

class CommandHandlerNotFoundException extends RuntimeException implements NotFoundException
{

}
