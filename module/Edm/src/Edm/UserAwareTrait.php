<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm;

use Edm\Model\AbstractModel,
    Edm\Auth\AuthServiceAwareTrait;

/**
 * Getter and Setters for a user model.  
 * ** Note ** Assumes "user model class name" is used in class; 
 * I.e., use Edm\Model\"UserName" where "UserName" is your model's class name
 * @uses AuthServiceAwareTrait
 * @author ElyDeLaCruz
 */
trait UserAwareTrait {

    use AuthServiceAwareTrait;
    
    /**
     * User variable
     * @var Edm\Model\User
     */
    protected $user;

    /**
     * User Model Class Name to use in fetch
     * @var string
     */
    public $userModelClassName = 'UserModel';

    /**
     * Gets a user
     * @return Edm\Model\Abstract
     */
    public function getUser() {
        if (empty($this->user)) {
            $this->user = new $this->userModelClassName();
        }
        return $this->user;
    }

    /**
     * Set our user model
     * @param \Edm\Model\AbstractModel $user
     */
    public function setUser(AbstractModel $user) {
        $this->user = $user;
    }

    /**
     * Set a user model from an array
     * @param array $user
     * @param boolean $fresh false (use a fresh copy)
     */
    public function setUserFromArray(array $user, $fresh = false) {
        if ($fresh) {
            unset($this->user);
        }
        $this->getUser()->exhangeArray($user);
    }

}

