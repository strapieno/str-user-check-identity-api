<?php
namespace Strapieno\UserCheckIdentity\Api\V1\Listener;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $config = $serviceLocator->get('Config');

        if (!isset($config['user_listener']['template'])) {
           throw new ServiceNotCreatedException('Template not set in EmailListenerFactory');
        }

        if (!isset($config['user_listener']['check-identity-subject'])) {
            throw new ServiceNotCreatedException('Subject not set in EmailListenerFactory');
        }

        $listener = new EmailListener();
        $listener->setSubject($config['user_listener']['check-identity-subject']);
        $listener->setTemplate($config['user_listener']['template']);
        return $listener;
    }
}