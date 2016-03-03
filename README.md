# CQRS library

[![Latest Stable Version](https://poser.pugx.org/pauci/cqrs/v/stable)](https://packagist.org/packages/pauci/cqrs)
[![Total Downloads](https://poser.pugx.org/pauci/cqrs/downloads)](https://packagist.org/packages/pauci/cqrs)
[![Build Status](https://travis-ci.org/pauci/cqrs.svg?branch=master)](https://travis-ci.org/pauci/cqrs)
[![Coverage Status](https://coveralls.io/repos/pauci/cqrs/badge.png?branch=master)](https://coveralls.io/r/pauci/cqrs)

## Installation & Requirements

The core library has no dependencies on other libraries. Plugins have dependencies on their specific libraries.

Install with [composer](http://getcomposer.org):

    composer require pauci/cqrs dev-master


## Usage

```php

class User extends CQRS\Domain\Model\AbstractAggregateRoot
{
    private $name;

    public function changeName($name)
    {
        $oldName = $this->name;
        $this->name = $name;

        $this->registerEvent(new UserNameChanged(['name' => $name, 'oldName' => $name]));
    }
}

class ChangeUserName extends CQRS\Domain\Payload\AbstractCommand
{
    public $id;
    public $name;
}

class UserNameChanged extends CQRS\Domain\Payload\AbstractEvent
{
    public $id;
    public $name;
    public $oldName;
}

class UserService
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function changeUserName(ChangeUserName $command)
    {
        $user = $this->repository->find($command->id);
        $user->changeName($command->name);
    }
}

class EchoEventListener
{
    public function onUserNameChanged(UserNameChanged $event)
    {
        echo "Name of user #{$event->id} changed from {$event->oldName} to {$event->name}.\n";
    }
}

$command = new ChangeUserName([
    'id' => 1,
    'name' => 'Jozko Mrkvicka',
]);
$commandBus->dispatch($command);
```
