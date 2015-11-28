<?php

namespace Edm\Auth;

/**
 * Description of AuthServiceAware
 *
 * @author ElyDeLaCruz
 */
trait AuthServiceAwareTrait {
   
    /**
     * Authentication Service
     * @var \Zend\Authentication\AuthenticationServiceInterface
     */
    protected $authService;
    
    /**
     * Authentication Service Class Name
     * @var \Zend\Authentication\AuthenticationService
     */
    public $authServiceClassName = 'Zend\Authentication\AuthenticationService';

    /**
     * Gets our Authentication Service
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService() {
        if (!isset($this->authService)) {
            $this->authService = 
                    $this->getServiceLocator()
                        ->get($this->authServiceClassName);
        }
        return $this->authService;
    }

    /**
     * @param ServiceInterface $authService
     */
    public function setAuthService(ServiceInterface $authService) {
        $this->authService = $authService;
    }
    
}
