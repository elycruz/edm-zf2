<?php

namespace Edm\Service;

use Edm\Service\AbstractService;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Assumes service locator aware interface
 * @author ElyDeLaCruz
 */
trait UserServiceAwareTrait {

    /**
     * User Service
     * @var Edm\Service\AbstractService
     */
    protected $userService;
    
    /**
     * User Service Name used to get the user service
     * @var string 
     */
    public $userServiceClassName = 'Edm\Service\UserService';
    
    /**
     * Gets our user service
     * @return Edm\Serivce\AbstractService
     */
    public function getUserService() {
        if (empty($this->userService)) {
            $this->userService = 
                    $this->getServiceLocator()
                        ->get($this->userServiceClassName);
        }
        return $this->userService;
    }

    
    public function setUserService(AbstractService $userService) {
        $this->userService = $userService;
    }
    
}
