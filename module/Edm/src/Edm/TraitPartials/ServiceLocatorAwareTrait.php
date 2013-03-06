<?php
namespace Edm\TraitPartials;;

use Zend\ServiceManager\ServiceLocatorInterface;

trait ServiceLocatorAwareTrait {
    /**
     * Set serviceManager instance
     * @param  ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve serviceManager instance
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
