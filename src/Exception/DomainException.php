<?php

declare(strict_types=1);

namespace CQRS\Exception;

class DomainException extends \DomainException implements ExceptionInterface
{
}
