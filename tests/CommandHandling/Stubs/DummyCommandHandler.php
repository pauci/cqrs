<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling\Stubs;

use CQRS\CommandHandling\SequentialCommandBus;
use CQRSTest\CommandHandling\Stubs;

class DummyCommandHandler
{
    public SequentialCommandBus $commandBus;

    public int $simple = 0;

    public int $sequential = 0;

    public function doSimple(Stubs\DoSimpleCommand $command): void
    {
        $this->simple++;
    }

    public function doSequential(Stubs\DoSequentialCommand $command): void
    {
        $this->sequential++;
        $this->commandBus->dispatch(new Stubs\DoSimpleCommand());
    }

    public function doFailure(Stubs\DoFailureCommand $command): void
    {
        throw new CommandFailureTestException();
    }

    public function doSequentialFailure(Stubs\DoSequentialFailureCommand $command): void
    {
        $this->commandBus->dispatch(new Stubs\DoFailureCommand());
    }
}
