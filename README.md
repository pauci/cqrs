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

class ChangeUserName
{
    public $id;
    public $name;
}

class UserNameChanged
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
