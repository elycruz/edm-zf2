<?php

namespace Edm\Auth;

use Zend\Authentication\AuthenticationService,
    Zend\Authentication\AuthenticationServiceInterface;

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
     * Gets our Authentication Service
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService() {
        if (!isset($this->authService)) {
            $this->authService = new AuthenticationService();
        }
        return $this->authService;
    }

    /**
     * @param ServiceInterface $authService
     */
    public function setAuthService(AuthenticationServiceInterface $authService) {
        $this->authService = $authService;
    }
    
}
