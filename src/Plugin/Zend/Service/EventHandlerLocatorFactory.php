<?php

namespace CQRS\Plugin\Zend\Service;

use CQRS\Plugin\Zend\Options\EventHandlerLocator as EventHandlerLocatorOptions;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventHandlerLocatorFactory extends AbstractFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \CQRS\CommandHandling\CommandHandlerLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EventHandlerLocatorOptions $options */
        $options = $this->getOptions($serviceLocator, 'eventHandlerLocator');
        return $this->create($serviceLocator, $options);
    }

    /**
     * @return string
     */
    public function getOptionsClass()
    {
        return 'CQRS\Plugin\Zend\Options\EventHandlerLocator';
    }

    /**
     * @param ServiceLocatorInterface $sl
     * @param EventHandlerLocatorOptions $options
     * @return \CQRS\EventHandling\EventHandlerLocator
     * @throws RuntimeException
     */
    protected function create(ServiceLocatorInterface $sl, EventHandlerLocatorOptions $options)
    {
        $class = $options->getClass();

        if (!$class) {
            throw new RuntimeException('EventHandlerLocator must have a class name to instantiate');
        }

        /** @var \CQRS\EventHandling\EventHandlerLocator $eventHandlerLocator */
        $eventHandlerLocator = new $class;

        if ($eventHandlerLocator instanceof ServiceLocatorAwareInterface) {
            $eventHandlerLocator->setServiceLocator($sl);
        }

        $callbacks = $options->getCallbacks();

        foreach ($callbacks as $eventName => $callback) {
            $priority = 1;
            if (is_array($callback) && isset($callback['callback'])) {
                if (isset($callback['event'])) {
                    $eventName = $callback['event'];
                }
                if (isset($callback['priority'])) {
                    $priority = $callback['priority'];
                }
                $callback = $callback['callback'];
            }
            $eventHandlerLocator->registerCallback($eventName, $callback, $priority);
        }

        return $eventHandlerLocator;
    }
}
