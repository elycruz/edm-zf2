<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Auth;

/**
 * Description of AuthServiceAware
 *
 * @author ElyDeLaCruz
 */
trait AuthServiceAwareTrait {
   
    /**
     * Authentication Service
     * @var Zend\Authentication\Service\ServiceInterface
     */
    protected $authService;
    
    /**
     * Authentication Service Class Name
     * @var Zend\Authentication\AuthenticationService
     */
    public $authServiceClassName = 'Zend\Authentication\AuthService';

    /**
     * Gets our Authentication Service
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthService() {
        if (empty($this->authService)) {
            $this->authService = 
                    $this->getServiceLocator()
                        ->get($this->authServiceClassName);
        }
        return $this->authService;
    }

    public function setAuthService(ServiceInterface $authService) {
        $this->authService = $authService;
    }
    
}
