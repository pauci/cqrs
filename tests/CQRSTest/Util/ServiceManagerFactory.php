<?php

namespace CQRSTest\Util;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * Utility used to retrieve a freshly bootstrapped application's service manager
 */
class ServiceManagerFactory
{
    /** @var array */
    protected static $config = [];

    /**
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        static::$config = $config;
    }

    /**
     * Builds a new service manager
     *
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        $serviceManager = new ServiceManager(
            new ServiceManagerConfig(
                isset(static::$config['service_manager']) ? static::$config['service_manager'] : []
            )
        );
        $serviceManager->setService('ApplicationConfig', static::$config);
        $serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

        /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        return $serviceManager;
    }
}
