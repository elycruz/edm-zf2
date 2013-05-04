<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Auth;

use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\AuthenticationService;

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
     * Gets our Authentication Service
     * @return Zend\Authentication\AuthenticationService
     */
    public function getAuthService() {
        if (empty($this->authService)) {
            $this->authService = new AuthenticationService();
        }
        return $this->authService;
    }
    
    public function setAuthService(AdapterInterface $authService) {
        $this->authService = $authService;
    }
    
}
