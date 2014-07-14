<?php
namespace CQRSTest\Domain;

use CQRS\Domain\AbstractProtectedPropertyReadAccessObject;

class AbstractProtectedPropertyReadAccessObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToProperties()
    {
        $event = new TestObject([
            'test'          => 'value',
            'protectedTest' => 'protectedValue'
        ]);

        $this->assertEquals('value', $event->test);
        $this->assertEquals('protectedValue', $event->protectedTest);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on object "CQRSTest\Domain\TestObject"'
        );

        new TestObject(['unknown' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $event = new TestObject();

        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on object "CQRSTest\Domain\TestObject"'
        );

        $value = $event->unknown;
    }
}

/**
 * @property-read string $protectedTest
 */
class TestObject extends AbstractProtectedPropertyReadAccessObject
{
    public $test;

    protected $protectedTest;
}
