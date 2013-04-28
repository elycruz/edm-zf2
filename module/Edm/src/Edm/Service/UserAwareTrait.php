<?php

namespace Edm\Service;

use Edm\Service\AbstractService;

/**
 * Description of UserAwareTrait
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait UserAwareTrait {
    
    /**
     * User Service
     * @var Edm\Service\UserService
     */
    protected $userService;
    
    /**
     * Laxy loads our User Service
     * @return Edm\Service\UserService
     */
    public function getUserService () {
        if (!isset($this->userService)) {
            $this->userService = $this->getServiceLocator()
                    ->get('Edm\Service\UserService');
        }
        return $this->userService;
    }
    
    public function setUserService (AbstractService $service) {
        $this->userService = $service;
        return $this;
    }
}
