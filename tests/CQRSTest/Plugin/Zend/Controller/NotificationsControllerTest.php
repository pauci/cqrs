<?php

namespace CQRSTest\Plugin\Zend\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class NotificationsControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../configuration.php.dist'
        );

        parent::setUp();
    }

    public function testGetAll()
    {
        $this->dispatch('/cqrs/notifications');

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('CQRS');
        $this->assertControllerName('CQRS\Controller\Notifications');
        $this->assertControllerClass('NotificationsController');
        $this->assertMatchedRouteName('cqrs/notifications');
    }
} 
