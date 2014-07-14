# CQRS library


## Installation & Requirements

The core library has no dependencies on other libraries. Plugins have dependencies on their specific libraries.

Install with [Composer](http://getcomposer.org):

    {
        "require": {
            "sygic/cqrs": "dev-master"
        }
    }

## Setup
```php
return [
    'cqrs' => [
        'commandHandlerLocator' => [
            'cqrs_default' => [
                'handlers' => [
                    'UserService' => [
                        'ChangeUserName'
                    ]
                ]
            ]
        ],
        'eventHandlerLocator' => [
            'cqrs_default' => [
                'services' => [
                    'EchoEventListener' => [
                        'UserNameChanged'
                    ]
                ]
            ]
        ]
    ]
];
```



## Usage

```php

class User extends CQRS\Domain\Model\AbstractAggregateRoot
{
    private $name;

    public function changeName($name)
    {
        $oldName = $this->name;
        $this->name = $name;

        $this->raiseDomainEvent(new UserNameChanged(['name' => $name, 'oldName' => $name]));
    }
}

class ChangeUserName extends CQRS\CommandHandling\DefaultCommand
{
    public $id;
    public $name;
}

class UserNameChanged extends CQRS\Domain\Message\AbstractDomainEventMessage
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

$commandBus = $this->getServiceLocator()->get('cqrs.commandBus.cqrs_default');

$command = new ChangeUserName(['id' => 1, 'name' => 'Jozko Mrkvicka']);
$commandBus->handler($command);
```
