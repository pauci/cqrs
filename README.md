# CQRS library

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]


## Installation & Requirements

The core library has no dependencies on other libraries. Plugins have dependencies on their specific libraries.

Install with [composer](http://getcomposer.org):

```shell script
composer require pauci/cqrs
```


## Usage

```php

final class User extends \CQRS\Domain\Model\AbstractEventSourcedAggregateRoot
{
    private int $id;
    private string $name;

    public static function create(int $id, string $name): self
    {
        $user = new self($id);
        $user->apply(new UserCreated($name));
        return $user;
    }

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    protected function applyUserCreated(UserCreated $event): void
    {
        $this->name = $event->getName();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function changeName(string $name): void
    {
        if ($name !== $this->name) {
            $this->apply(new UserNameChanged($name));
        }
    }

    protected function applyUserNameChanged(UserNameChanged $event): void
    {
        $this->name = $event->getName();
    }
}

final class UserCreated
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

final class ChangeUserName
{
    public int $id;
    public string $name;
}

final class UserNameChanged
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
}

class UserService
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function changeUserName(ChangeUserName $command): void
    {
        $user = $this->repository->find($command->id);
        $user->changeName($command->name);
    }
}

class EchoEventListener
{
    public function onUserNameChanged(
        UserNameChanged $event,
        \CQRS\Domain\Message\Metadata $metadata,
        \DateTimeInterface $timestamp,
        int $sequenceNumber,
        int $userId
    ): void {
        echo "Name of user #{$userId} changed to {$event->getName()}.\n";
    }
}

$command = new ChangeUserName([
    'id' => 1,
    'name' => 'Jozko Mrkvicka',
]);
$commandBus->dispatch($command);
```


[badge-source]: https://img.shields.io/badge/source-pauci/cqrs-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/pauci/cqrs.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/pauci/cqrs/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/pauci/cqrs/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/pauci/cqrs.svg?style=flat-square

[source]: https://github.com/pauci/cqrs
[release]: https://packagist.org/packages/pauci/cqrs
[license]: https://github.com/pauci/cqrs/blob/master/LICENSE
[build]: https://travis-ci.org/pauci/cqrs
[coverage]: https://coveralls.io/r/pauci/cqrs?branch=master
[downloads]: https://packagist.org/packages/pauci/cqrs
