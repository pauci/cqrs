<?php

namespace CQRS\Plugin\Zend\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class NotificationsController extends AbstractRestfulController
{
    public function getList()
    {
        $data = ['foo' => 'bar'];

        return new JsonModel($data);
    }
} 
